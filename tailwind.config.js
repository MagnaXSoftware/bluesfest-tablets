/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ["./templates/**/*.html.twig", "./public/js/*.js"],
    theme: {
        extend: {
            colors: {
                "lightblue": "#ADD8E6"
            }
        },
    },
    plugins: [
        require('@tailwindcss/forms')
    ],
}
