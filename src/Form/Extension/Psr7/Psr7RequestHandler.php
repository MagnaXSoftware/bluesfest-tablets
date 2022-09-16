<?php

namespace App\Form\Extension\Psr7;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\RequestHandlerInterface;
use Symfony\Component\Form\Util\ServerParams;
use function count;
use function is_array;

class Psr7RequestHandler implements RequestHandlerInterface
{
    private $serverParams;

    public function __construct(ServerParams $serverParams = null)
    {
        $this->serverParams = $serverParams ?? new ServerParams();
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest(FormInterface $form, $request = null)
    {
        if (!$request instanceof ServerRequestInterface) {
            throw new UnexpectedTypeException($request, ServerRequestInterface::class);
        }

        $name = $form->getName();
        $method = $form->getConfig()->getMethod();

        if ($method !== $request->getMethod()) {
            return;
        }

        // For request methods that must not have a request body we fetch data
        // from the query string. Otherwise we look for data in the request body.
        if ('GET' === $method || 'HEAD' === $method || 'TRACE' === $method) {
            if ('' === $name) {
                $data = $request->getQueryParams();
            } else {
                // Don't submit GET requests if the form's name does not exist
                // in the request
                $data = $request->getQueryParams()[$name] ?? null;
                if ($data === null) {
                    return;
                }
            }
        } else {
            // Mark the form with an error if the uploaded size was too large
            // This is done here and not in FormValidator because $_POST is
            // empty when that error occurs. Hence the form is never submitted.
            if ($this->serverParams->hasPostMaxSizeBeenExceeded()) {
                // Submit the form, but don't clear the default values
                $form->submit(null, false);

                $form->addError(new FormError(
                    $form->getConfig()->getOption('upload_max_size_message')(),
                    null,
                    ['{{ max }}' => $this->serverParams->getNormalizedIniPostMaxSize()]
                ));

                return;
            }

            if ('' === $name) {
                $params = $request->getParsedBody();
                $files = $request->getUploadedFiles();
            } elseif (isset($request->getParsedBody()[$name]) || isset($request->getUploadedFiles()[$name])) {
                $default = $form->getConfig()->getCompound() ? [] : null;
                $params = $request->getParsedBody()[$name] ?? $default;
                $files = $request->getUploadedFiles()[$name] ?? $default;
            } else {
                // Don't submit the form if it is not present in the request
                return;
            }

            if (is_array($params) && is_array($files)) {
                $data = array_replace_recursive($params, $files);
            } else {
                $data = $params ?: $files;
            }
        }

        // Don't auto-submit the form unless at least one field is present.
        if ('' === $name && count(array_intersect_key($data, $form->all())) <= 0) {
            return;
        }

        $form->submit($data, 'PATCH' !== $method);
    }

    /**
     * {@inheritdoc}
     */
    public function isFileUpload($data): bool
    {
        return $data instanceof UploadedFileInterface;
    }

    /**
     * @param $data
     * @return ?int
     */
    public function getUploadFileError($data): ?int
    {
        if (!$data instanceof UploadedFileInterface || $data->getError() === UPLOAD_ERR_OK) {
            return null;
        }

        return $data->getError();
    }
}