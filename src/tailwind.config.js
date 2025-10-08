const defaultTheme = require("tailwindcss/defaultTheme");

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
        },
        screens: {
            x3lmax: { max: "2550px" },
            // => @media (max-width: 1535px) { ... }

            x2lmax: { max: "1650px" },
            // => @media (max-width: 1279px) { ... }

            xlmax: { max: "1279px" },
            // => @media (max-width: 1279px) { ... }

            lgmax: { max: "1023px" },
            // => @media (max-width: 1023px) { ... }

            mdmax: { max: "767px" },
            // => @media (max-width: 767px) { ... }

            smax: { max: "639px" },
            // => @media (max-width: 639px) { ... }

            sm: "640px",
            // => @media (min-width: 640px) { ... }

            md: "768px",
            // => @media (min-width: 768px) { ... }

            lg: "1024px",
            // => @media (min-width: 1024px) { ... }

            xl: "1280px",
            // => @media (min-width: 1280px) { ... }

            "2xl": "1536px",
            // => @media (min-width: 1536px) { ... }
        },
    },

    plugins: [require("@tailwindcss/forms")],
};
