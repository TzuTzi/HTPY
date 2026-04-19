/* ============================================================
   data.js  –  Centralized data for BlimBlau
   Used by: carousel.js, table-sort.js, mood-picker.js,
            dependencies.js
   ============================================================ */

/* ── Films (used by table-sort.js and mood-picker.js) ── */
const films = [
    { title: "Dune: Part Two",              year: 2024, genre: "Sci-Fi",   rating: 8.5, reviews: 412, mood: "adventurous" },
    { title: "Oppenheimer",                 year: 2023, genre: "Drama",    rating: 8.9, reviews: 897, mood: "tense"       },
    { title: "Poor Things",                 year: 2023, genre: "Comedy",   rating: 8.0, reviews: 534, mood: "happy"       },
    { title: "The Batman",                  year: 2022, genre: "Action",   rating: 7.9, reviews: 723, mood: "tense"       },
    { title: "Everything Everywhere",       year: 2022, genre: "Comedy",   rating: 9.0, reviews: 1021, mood: "happy"      },
    { title: "Killers of the Flower Moon",  year: 2023, genre: "Drama",    rating: 7.7, reviews: 345, mood: "melancholic" },
    { title: "Parasite",                    year: 2019, genre: "Thriller", rating: 8.6, reviews: 1150, mood: "tense"      },
    { title: "The Lighthouse",              year: 2019, genre: "Horror",   rating: 7.5, reviews: 289, mood: "melancholic" },
    { title: "Arrival",                     year: 2016, genre: "Sci-Fi",   rating: 7.9, reviews: 678, mood: "melancholic" },
    { title: "The Grand Budapest Hotel",    year: 2014, genre: "Comedy",   rating: 8.1, reviews: 821, mood: "happy"       },
    { title: "Mad Max: Fury Road",          year: 2015, genre: "Action",   rating: 8.1, reviews: 934, mood: "adventurous" },
    { title: "Interstellar",               year: 2014, genre: "Sci-Fi",   rating: 8.7, reviews: 1432, mood: "adventurous" },
    { title: "Midsommar",                   year: 2019, genre: "Horror",   rating: 7.1, reviews: 312, mood: "melancholic" },
    { title: "Knives Out",                  year: 2019, genre: "Thriller", rating: 7.9, reviews: 765, mood: "happy"       },
    { title: "1917",                        year: 2019, genre: "Drama",    rating: 8.3, reviews: 567, mood: "tense"       },
];

/* ── Cities per country (used by dependencies.js) ── */
const cities = {
    ro: ["Cluj-Napoca", "București", "Timișoara", "Iași", "Brașov", "Constanța"],
    md: ["Chișinău", "Bălți", "Cahul", "Orhei", "Soroca"],
    us: ["New York", "Los Angeles", "Chicago", "Houston", "Phoenix"],
    uk: ["London", "Manchester", "Birmingham", "Leeds", "Glasgow"],
    fr: ["Paris", "Lyon", "Marseille", "Toulouse", "Nice"],
    de: ["Berlin", "Hamburg", "Munich", "Cologne", "Frankfurt"],
    it: ["Rome", "Milan", "Naples", "Turin", "Florence"],
    es: ["Madrid", "Barcelona", "Valencia", "Seville", "Zaragoza"],
};

/* ── Carousel slides (used by carousel.js) ── */
const carouselSlides = [
    {
        title: "Dune: Part Two",
        year: "2024",
        genre: "Sci-Fi / Adventure",
        color: "#c8a96e",
        link: "featured-html5.html",
        imageFile: "dune-part-two.jpg",      // ← filename for poster
        imageAlt: "Dune Part Two poster"
    },
    {
        title: "Oppenheimer",
        year: "2023",
        genre: "Drama / History",
        color: "#e05c2a",
        link: "featured-html5.html",
        imageFile: "oppenheimer.jpg",
        imageAlt: "Oppenheimer movie poster"
    },
    {
        title: "Poor Things",
        year: "2023",
        genre: "Comedy / Fantasy",
        color: "#6eafc8",
        link: "featured-html5.html",
        imageFile: "poor-things.png",
        imageAlt: "Poor Things poster"
    },
    {
        title: "Parasite",
        year: "2019",
        genre: "Thriller / Drama",
        color: "#4e9b4e",
        link: "featured-html5.html",
        imageFile: "parasite.jpg",
        imageAlt: "Parasite movie poster"
    },
    {
        title: "Interstellar",
        year: "2014",
        genre: "Sci-Fi / Drama",
        color: "#5a7fa8",
        link: "featured-html5.html",
        imageFile: "interstellar.png",
        imageAlt: "Interstellar poster"
    },
];

/* ── Mood labels and colors (used by mood-picker.js) ── */
const moods = [
    { key: "happy",       label: "Happy",        color: "#f0b429", desc: "Light, fun, uplifting films"       },
    { key: "tense",       label: "Tense",         color: "#e05c2a", desc: "Thrillers, suspense, edge-of-seat" },
    { key: "melancholic", label: "Melancholic",   color: "#5a7fa8", desc: "Emotional, reflective dramas"      },
    { key: "adventurous", label: "Adventurous",   color: "#3fb950", desc: "Action, sci-fi, epic journeys"     },
];
