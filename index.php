<?php
session_start();

require_once 'config/constant.php';
require_once 'config/db.php';

$page_title = "Home";

/* ==========================================
   BANNERS - Dynamic Hero Section with Swiper
========================================== */

$banners = $database->select("banners", "*", [
    "status" => "Active",
    "ORDER" => [
        "id" => "ASC"
    ]
]);

/* ==========================================
   Categories
========================================== */

$categories = $database->select("categories", "*", [
    "status" => "Active",
    "ORDER" => [
        "id" => "DESC"
    ]
]);

/* ==========================================
   Best Selling Products
========================================== */

$bestSelling = $database->select("products", "*", [
    "is_best_selling" => 1,
    "status" => "Active",
    "ORDER" => [
        "id" => "DESC"
    ],
    "LIMIT" => 8
]);

/* ==========================================
   New Arrival Products
========================================== */

$newArrival = $database->select("products", "*", [
    "is_new_arrival" => 1,
    "status" => "Active",
    "ORDER" => [
        "id" => "DESC"
    ],
    "LIMIT" => 8
]);

/* ==========================================
   Trending Products
========================================== */

$trendingProducts = $database->select("products", "*", [
    "is_trending" => 1,
    "status" => "Active",
    "ORDER" => [
        "id" => "DESC"
    ],
    "LIMIT" => 8
]);

/* ==========================================
   Customer Favorite Products
========================================== */

$customerFavorite = $database->select("products", "*", [
    "is_customer_favorite" => 1,
    "status" => "Active",
    "ORDER" => [
        "id" => "DESC"
    ],
    "LIMIT" => 8
]);

/* ==========================================
   Category Count
========================================== */

foreach ($categories as &$category) {
    $category['product_count'] = $database->count("products", [
        "category_id" => $category['id'],
        "status" => "Active"
    ]);
}
unset($category);

/* ==========================================
   Totals (used in hero stats) - fetched once
========================================== */
$totalProducts   = $database->count("products");
$totalCategories = $database->count("categories");

/* ==========================================
   Helper: safely format a price
========================================== */
function formatPrice($value)
{
    return number_format((float) ($value ?? 0), 2);
}

include 'includes/header.php';
?>

<!-- ============================================================
     AOS & SWIPER LIBRARY CDN
============================================================ -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<style>
    /* ==========================================================
       HERO BANNER
    ========================================================== */
    .hero-swiper {
        width: 100%;
        height: 100vh;
        min-height: 100vh;
    }

    .hero-swiper .swiper-slide {
        position: relative;
        width: 100%;
        height: 100%;
    }

    .hero-swiper .slide-bg {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transform: scale(1.08);
        transition: transform 6s ease-out;
    }

    .hero-swiper .swiper-slide-active .slide-bg {
        transform: scale(1);
    }

    .hero-swiper .slide-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
    }

    .hero-swiper .slide-gradient {
        position: absolute;
        inset: 0;
        background: linear-gradient(to right, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.5), transparent);
    }

    .hero-swiper .slide-content {
        position: relative;
        z-index: 10;
        display: flex;
        align-items: center;
        height: 100vh;
        padding: 0 2rem;
    }

    .hero-swiper .slide-content .container {
        width: 100%;
    }

    .hero-swiper .hero-title {
        font-size: 4.5rem;
        line-height: 1.1;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 0.5rem;
    }

    .hero-swiper .hero-subtitle {
        font-size: 2.2rem;
        line-height: 1.3;
        font-weight: 400;
        color: #C6A769;
        display: block;
        margin-top: 0.25rem;
        letter-spacing: 1px;
    }

    .hero-swiper .hero-subtitle-alt {
        font-size: 2rem;
        line-height: 1.3;
        font-weight: 300;
        color: #E7D3A8;
        display: block;
        font-style: italic;
        letter-spacing: 2px;
        margin-top: 0.25rem;
    }

    .hero-swiper .hero-description {
        max-width: 500px;
        font-size: 1.125rem;
        line-height: 1.8;
        color: #d1d5db;
        margin-top: 1.5rem;
    }

    .hero-swiper .hero-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 1.25rem;
        margin-top: 2.5rem;
    }

    .hero-swiper .hero-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
        margin-top: 3.5rem;
    }

    .hero-swiper .hero-stats .stat-number {
        font-size: 2.25rem;
        font-weight: 700;
        color: #ffffff;
    }

    .hero-swiper .hero-stats .stat-label {
        margin-top: 0.5rem;
        color: #9ca3af;
    }

    .hero-swiper .btn-primary {
        background: #C6A769;
        color: #000000;
        padding: 1rem 2rem;
        border-radius: 9999px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .hero-swiper .btn-primary:hover {
        background: #ffffff;
        transform: scale(1.05);
    }

    .hero-swiper .btn-secondary {
        border: 1px solid #ffffff;
        color: #ffffff;
        padding: 1rem 2rem;
        border-radius: 9999px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .hero-swiper .btn-secondary:hover {
        background: #C6A769;
        border-color: #C6A769;
        color: #000000;
        transform: scale(1.05);
    }

    .hero-swiper .badge {
        display:contents;
        align-items: center;
        gap: 0.5rem;
        border: 1px solid #C6A769;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(4px);
        padding: 0.5rem 1.25rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        color: #E7D3A8;
        margin-bottom: 1rem;
        animation: badgePulse 2.5s ease-in-out infinite;
    }

    @keyframes badgePulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(198, 167, 105, 0.4); }
        50% { box-shadow: 0 0 0 8px rgba(198, 167, 105, 0); }
    }

    .hero-swiper .swiper-button-next,
    .hero-swiper .swiper-button-prev {
        color: #C6A769;
        background: rgba(0, 0, 0, 0.5);
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: 2px solid rgba(198, 167, 105, 0.3);
        transition: all 0.3s ease;
    }

    .hero-swiper .swiper-button-next:hover,
    .hero-swiper .swiper-button-prev:hover {
        background: #C6A769;
        color: #000000;
        border-color: #C6A769;
        transform: scale(1.08);
    }

    .hero-swiper .swiper-button-next::after,
    .hero-swiper .swiper-button-prev::after {
        font-size: 1.2rem;
        font-weight: bold;
    }

    .hero-swiper .swiper-pagination-bullet {
        background: rgba(255, 255, 255, 0.5);
        opacity: 1;
        width: 12px;
        height: 12px;
        transition: all 0.3s ease;
    }

    .hero-swiper .swiper-pagination-bullet-active {
        background: #C6A769;
        transform: scale(1.2);
    }

    .hero-swiper .swiper-pagination {
        bottom: 30px;
    }

    [data-aos] {
        opacity: 0;
        transition-property: opacity, transform;
    }

    [data-aos].aos-animate {
        opacity: 1;
    }

    .hero-swiper .swiper-slide-active .slide-content [data-aos] {
        opacity: 0;
    }

    .hero-swiper .swiper-slide-active .slide-content [data-aos].aos-animate {
        opacity: 1;
    }

    /* ==========================================================
       SECTION SPACING (defined here explicitly so gaps always
       show, even if the external stylesheet doesn't define it)
    ========================================================== */
    .section-spacing {
        padding-top: 6rem;
        padding-bottom: 6rem;
    }

    @media (max-width: 768px) {
        .section-spacing {
            padding-top: 3.5rem;
            padding-bottom: 3.5rem;
        }
    }

    /* Swiper carousel sections (Best Seller / Testimonials) */
    .bestSellerSwiper,
    .testimonialSwiper {
        padding-bottom: 3rem;
    }

    .bestSellerSwiper .swiper-slide,
    .testimonialSwiper .swiper-slide {
        height: auto;
        display: flex;
    }

    .bestSellerSwiper .swiper-pagination-bullet,
    .testimonialSwiper .swiper-pagination-bullet {
        background: #C6A769;
        opacity: 0.4;
        width: 10px;
        height: 10px;
    }

    .bestSellerSwiper .swiper-pagination-bullet-active,
    .testimonialSwiper .swiper-pagination-bullet-active {
        opacity: 1;
        transform: scale(1.2);
    }

    .bestSellerSwiper .swiper-button-next,
    .bestSellerSwiper .swiper-button-prev,
    .testimonialSwiper .swiper-button-next,
    .testimonialSwiper .swiper-button-prev {
        color: #C6A769;
        background: #fff;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        box-shadow: 0 6px 18px rgba(0,0,0,0.12);
        transition: all 0.3s ease;
    }

    .bestSellerSwiper .swiper-button-next:hover,
    .bestSellerSwiper .swiper-button-prev:hover,
    .testimonialSwiper .swiper-button-next:hover,
    .testimonialSwiper .swiper-button-prev:hover {
        background: #C6A769;
        color: #000;
        transform: scale(1.08);
    }

    .bestSellerSwiper .swiper-button-next::after,
    .bestSellerSwiper .swiper-button-prev::after,
    .testimonialSwiper .swiper-button-next::after,
    .testimonialSwiper .swiper-button-prev::after {
        font-size: 1rem;
        font-weight: bold;
    }

    /* Testimonial card */
    .testimonial-card {
        background: #fff;
        border-radius: 1.5rem;
        padding: 2.5rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.06);
        width: 100%;
        display: flex;
        flex-direction: column;
        transition: transform 0.4s ease, box-shadow 0.4s ease;
    }

    .testimonial-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }

    .testimonial-card .stars i {
        color: #C6A769;
        font-size: 0.9rem;
    }

    .testimonial-card .avatar {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #C6A769;
    }

    /* ==========================================================
       PRODUCT CARD ANIMATIONS (previously used but never defined)
    ========================================================== */
    .product-card-hover {
        will-change: transform;
        transition: transform 0.5s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.5s ease;
    }

    .product-image-wrapper {
        position: relative;
        overflow: hidden;
    }

    .product-image-wrapper img {
        transition: transform 0.7s cubic-bezier(0.22, 1, 0.36, 1), filter 0.5s ease;
    }

    .product-card-hover:hover .product-image-wrapper img {
        transform: scale(1.12);
    }

    .product-image-wrapper::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.25), transparent 40%);
        opacity: 0;
        transition: opacity 0.4s ease;
        pointer-events: none;
    }

    .product-card-hover:hover .product-image-wrapper::after {
        opacity: 1;
    }

    /* Wishlist heart pop */
    .product-card-hover button i.fa-heart {
        transition: transform 0.3s ease;
    }

    .product-card-hover button:hover i.fa-heart {
        transform: scale(1.25);
    }

    /* Add To Cart shimmer */
    .btn-shimmer {
        position: relative;
        overflow: hidden;
    }

    .btn-shimmer::before {
        content: "";
        position: absolute;
        top: 0;
        left: -75%;
        width: 50%;
        height: 100%;
        background: linear-gradient(120deg, transparent, rgba(255,255,255,0.35), transparent);
        transform: skewX(-20deg);
        transition: left 0.6s ease;
    }

    .btn-shimmer:hover::before {
        left: 130%;
    }

    /* Section fade/slide handled by AOS, this adds a subtle float on category cards */
    @keyframes floatArrow {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }

    .cat-arrow {
        animation: floatArrow 2.2s ease-in-out infinite;
    }

    /* Back to top button */
    #backToTop {
        position: fixed;
        right: 24px;
        bottom: 24px;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #C6A769;
        color: #000;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        opacity: 0;
        visibility: hidden;
        transform: translateY(20px);
        transition: all 0.35s ease;
        z-index: 50;
        box-shadow: 0 8px 20px rgba(0,0,0,0.25);
    }

    #backToTop.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    #backToTop:hover {
        background: #000;
        color: #C6A769;
        transform: translateY(-4px);
    }

    /* Newsletter input focus glow */
    .newsletter-input {
        transition: box-shadow 0.3s ease;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .hero-swiper .hero-title { font-size: 3.5rem; }
        .hero-swiper .hero-subtitle { font-size: 1.8rem; }
        .hero-swiper .hero-subtitle-alt { font-size: 1.6rem; }
    }

    @media (max-width: 768px) {
        .hero-swiper { height: 100vh; min-height: 100vh; }
        .hero-swiper .hero-title { font-size: 2.5rem; }
        .hero-swiper .hero-subtitle { font-size: 1.4rem; }
        .hero-swiper .hero-subtitle-alt { font-size: 1.2rem; }
        .hero-swiper .hero-stats { grid-template-columns: repeat(3, 1fr); gap: 1rem; }
        .hero-swiper .hero-stats .stat-number { font-size: 1.5rem; }
        .hero-swiper .slide-content { padding: 0 1rem; }
        .hero-swiper .swiper-button-next,
        .hero-swiper .swiper-button-prev { display: none; }
        #backToTop { right: 16px; bottom: 16px; width: 42px; height: 42px; }
    }
</style>

<!-- ============================================================
     HERO BANNER SWIPER SECTION
============================================================ -->

<div class="hero-swiper swiper">

    <div class="swiper-wrapper">

        <?php if (!empty($banners)): ?>

            <?php foreach ($banners as $index => $banner): ?>

                <div class="swiper-slide">

                    <img src="<?= BASE_URL ?>assets/uploads/banners/<?= htmlspecialchars($banner['image'] ?? ''); ?>"
                        alt="<?= htmlspecialchars($banner['title'] ?? 'Banner'); ?>"
                        class="slide-bg">

                    <div class="slide-overlay"></div>
                    <div class="slide-gradient"></div>

                    <div class="slide-content">
                        <div class="container mx-auto px-6 lg:px-10">

                            <div class="grid lg:grid-cols-2 items-center gap-12" style="min-height: 80vh;">

                                <div data-aos="fade-right" data-aos-duration="1000" data-aos-delay="100">

                                    <span class="badge">
                                        <i class="fa-solid fa-crown"></i>
                                        Luxury Collection <?= date('Y'); ?>
                                    </span>

                                    <h1 class="hero-title text-white">
                                        <?= htmlspecialchars($banner['title'] ?? ''); ?>
                                    </h1>

                                    <?php if (!empty($banner['subtitle'])): ?>
                                        <span class="hero-subtitle">
                                            <?= htmlspecialchars($banner['subtitle']); ?>
                                        </span>
                                    <?php endif; ?>

                                    <div class="hero-buttons">

                                        <?php if (!empty($banner['button_link'])): ?>
                                            <a href="<?= htmlspecialchars($banner['button_link']); ?>" class="btn-primary">
                                                <?= htmlspecialchars($banner['button_text'] ?? 'Shop Now'); ?>
                                            </a>
                                        <?php endif; ?>

                                        <a href="<?= BASE_URL ?>shop.php" class="btn-secondary">
                                            Explore Collection
                                        </a>

                                    </div>

                                    <?php if ($index === 0): ?>
                                        <div class="hero-stats">

                                            <div data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                                                <div class="stat-number counter" data-target="<?= (int) $totalProducts; ?>">0</div>
                                                <div class="stat-label">Products</div>
                                            </div>

                                            <div data-aos="fade-up" data-aos-duration="800" data-aos-delay="300">
                                                <div class="stat-number counter" data-target="<?= (int) $totalCategories; ?>">0</div>
                                                <div class="stat-label">Categories</div>
                                            </div>

                                            <div data-aos="fade-up" data-aos-duration="800" data-aos-delay="400">
                                                <div class="stat-number counter" data-target="100" data-suffix="%">0</div>
                                                <div class="stat-label">Quality</div>
                                            </div>

                                        </div>
                                    <?php endif; ?>

                                </div>

                                <div class="hidden lg:flex justify-center"
                                    data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">

                                    <img src="<?= BASE_URL ?>assets/uploads/banners/<?= htmlspecialchars($banner['image'] ?? ''); ?>"
                                        alt="<?= htmlspecialchars($banner['title'] ?? 'Banner'); ?>"
                                        class="w-full max-w-xl rounded-3xl shadow-2xl">

                                </div>

                            </div>

                        </div>
                    </div>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <!-- Default Banner (Fallback) -->
            <div class="swiper-slide">

                <img src="https://images.unsplash.com/photo-1539109136881-3be0616acf4b?w=1920&q=80"
                    alt="Luxury Fashion" class="slide-bg">

                <div class="slide-overlay"></div>
                <div class="slide-gradient"></div>

                <div class="slide-content">
                    <div class="container mx-auto px-6 lg:px-10">

                        <div class="grid lg:grid-cols-2 items-center gap-12" style="min-height: 80vh;">

                            <div data-aos="fade-right" data-aos-duration="1000" data-aos-delay="100">

                                <span class="badge">
                                    <i class="fa-solid fa-crown"></i>
                                    Luxury Collection <?= date('Y'); ?>
                                </span>

                                <h1 class="hero-title text-white">Premium Fashion</h1>

                                <span class="hero-subtitle">Designed For Every Occasion</span>

                                <p class="hero-description">
                                    Discover premium clothing, luxury accessories and modern fashion
                                    crafted with the finest quality for every lifestyle.
                                </p>

                                <div class="hero-buttons">
                                    <a href="<?= BASE_URL ?>shop.php" class="btn-primary">Shop Now</a>
                                    <a href="<?= BASE_URL ?>shop.php" class="btn-secondary">Explore Collection</a>
                                </div>

                                <div class="hero-stats">

                                    <div data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                                        <div class="stat-number counter" data-target="<?= (int) $totalProducts; ?>">0</div>
                                        <div class="stat-label">Products</div>
                                    </div>

                                    <div data-aos="fade-up" data-aos-duration="800" data-aos-delay="300">
                                        <div class="stat-number counter" data-target="<?= (int) $totalCategories; ?>">0</div>
                                        <div class="stat-label">Categories</div>
                                    </div>

                                    <div data-aos="fade-up" data-aos-duration="800" data-aos-delay="400">
                                        <div class="stat-number counter" data-target="100" data-suffix="%">0</div>
                                        <div class="stat-label">Quality</div>
                                    </div>

                                </div>

                            </div>

                            <div class="hidden lg:flex justify-center"
                                data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">

                                <img src="https://images.unsplash.com/photo-1539109136881-3be0616acf4b?w=700&q=80"
                                    alt="Luxury Fashion" class="w-full max-w-xl rounded-3xl shadow-2xl">

                            </div>

                        </div>

                    </div>
                </div>

            </div>

        <?php endif; ?>

    </div>

    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-pagination"></div>

</div>

<!-- ============================================================
     BRANDS SECTION
============================================================ -->

<section class="bg-white py-10" data-aos="fade-up" data-aos-duration="800">

    <div class="container mx-auto px-6">

        <h2 class="mb-8 text-center text-2xl font-semibold" data-aos="fade-up" data-aos-duration="600">
            Trusted Brands
        </h2>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8 items-center">

            <?php
            $brands = ["nike.png", "adidas.png", "puma.png", "zara.png", "hm.png", "levis.png"];

            foreach ($brands as $index => $brand):
            ?>

                <div class="flex justify-center" data-aos="zoom-in" data-aos-duration="600" data-aos-delay="<?= $index * 100 ?>">

                    <img src="<?= BASE_URL ?>assets/images/brands/<?= htmlspecialchars($brand); ?>"
                        class="h-10 opacity-60 grayscale transition duration-500 hover:opacity-100 hover:grayscale-0 hover:scale-110"
                        alt="Brand" loading="lazy">

                </div>

            <?php endforeach; ?>

        </div>

    </div>

</section>

<!-- ============================================================
     CATEGORIES SECTION
============================================================ -->

<section class="bg-[#F8F6F1] section-spacing" data-aos="fade-up" data-aos-duration="800">

    <div class="container mx-auto px-6">

        <div class="text-center mb-14" data-aos="fade-up" data-aos-duration="700">
            <span class="uppercase tracking-[4px] text-[#C6A769] text-sm">Categories</span>
            <h2 class="mt-3 text-4xl lg:text-5xl font-serif font-semibold">Shop By Category</h2>
            <p class="mt-4 text-gray-500 max-w-2xl mx-auto">
                Explore our premium collections carefully curated for every occasion.
            </p>
        </div>

        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">

            <?php if (!empty($categories)): ?>

                <?php foreach ($categories as $index => $cat): ?>

                    <a href="<?= BASE_URL ?>shop.php?category=<?= (int) $cat['id']; ?>"
                        class="group overflow-hidden rounded-3xl bg-white shadow hover:-translate-y-2 hover:shadow-2xl duration-500"
                        data-aos="fade-up" data-aos-duration="700" data-aos-delay="<?= $index * 100 ?>">

                        <div class="relative overflow-hidden">

                            <?php if (!empty($cat['image'])): ?>

                                <img src="<?= BASE_URL ?>assets/uploads/categories/<?= htmlspecialchars($cat['image']); ?>"
                                    alt="<?= htmlspecialchars($cat['category_name'] ?? ''); ?>"
                                    class="h-80 w-full object-cover duration-700 group-hover:scale-110" loading="lazy">

                            <?php else: ?>

                                <img src="<?= BASE_URL ?>assets/uploads/categories/no-image.png" alt="No Image"
                                    class="h-80 w-full object-cover duration-700 group-hover:scale-110">

                            <?php endif; ?>

                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>

                        </div>

                        <div class="p-6 flex items-center justify-between">

                            <div>
                                <h3 class="text-2xl font-serif font-semibold">
                                    <?= htmlspecialchars($cat['category_name'] ?? ''); ?>
                                </h3>
                                <p class="mt-2 text-gray-500">
                                    <?= (int) $cat['product_count']; ?> Products
                                </p>
                            </div>

                            <div class="cat-arrow h-12 w-12 rounded-full bg-[#C6A769] flex items-center justify-center transition duration-500 group-hover:rotate-45">
                                <i class="fa-solid fa-arrow-right text-black"></i>
                            </div>

                        </div>

                    </a>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="col-span-4 py-16 text-center">
                    <h3 class="text-2xl font-semibold text-gray-700">No Categories Available</h3>
                    <p class="mt-3 text-gray-500">Please add categories from the admin panel.</p>
                </div>

            <?php endif; ?>

        </div>

    </div>

</section>

<?php
/**
 * Reusable product grid renderer to keep the four product sections
 * (Best Selling / Customer Favorites / New Arrivals / Trending) consistent,
 * XSS-safe and null-safe.
 */
function renderProductCard($product, $index, $database, $badge = null, $cardBg = 'bg-white')
{
    $category = $database->get("categories", ["category_name"], ["id" => $product['category_id']]);
    $categoryName = htmlspecialchars($category['category_name'] ?? 'Category');
    $productName  = htmlspecialchars($product['product_name'] ?? '');
    $sellingPrice = (float) ($product['selling_price'] ?? 0);
    $discountPrice = (float) ($product['discount_price'] ?? 0);
    $hasDiscount = $discountPrice > 0 && $discountPrice < $sellingPrice;
    $discountPercent = $hasDiscount && $sellingPrice > 0
        ? round((($sellingPrice - $discountPrice) / $sellingPrice) * 100)
        : 0;
    $image = !empty($product['main_image'])
        ? BASE_URL . 'assets/uploads/products/' . htmlspecialchars($product['main_image'])
        : BASE_URL . 'assets/uploads/products/no-image.png';
    ?>
    <div class="group overflow-hidden rounded-3xl <?= $cardBg; ?> shadow hover:-translate-y-2 hover:shadow-2xl duration-500 product-card-hover"
        data-aos="fade-up" data-aos-duration="700" data-aos-delay="<?= ($index % 4) * 100 ?>">

        <div class="relative overflow-hidden product-image-wrapper">

            <img src="<?= $image; ?>" alt="<?= $productName; ?>" class="h-80 w-full object-cover" loading="lazy">

            <?php if ($badge): ?>
                <span class="absolute left-5 top-5 rounded-full <?= $badge['class']; ?> px-4 py-1 text-xs font-bold">
                    <?= $badge['label']; ?>
                </span>
            <?php endif; ?>

            <?php if ($hasDiscount): ?>
                <span class="absolute right-5 top-5 rounded-full bg-red-600 px-4 py-1 text-xs font-bold text-white">
                    <?= $discountPercent ?>% OFF
                </span>
            <?php endif; ?>

            <button class="absolute right-5 <?= $hasDiscount ? 'top-16' : 'top-5'; ?> flex h-10 w-10 items-center justify-center rounded-full bg-white shadow hover:bg-[#C6A769] hover:text-white transition">
                <i class="far fa-heart"></i>
            </button>

        </div>

        <div class="p-6">

            <p class="text-sm text-gray-500"><?= $categoryName; ?></p>

            <h3 class="mt-2 text-xl font-semibold line-clamp-1"><?= $productName; ?></h3>

            <div class="mt-4 flex items-center gap-3">
                <?php if ($hasDiscount): ?>
                    <span class="text-2xl font-bold text-[#C6A769]">₹<?= formatPrice($discountPrice); ?></span>
                    <span class="text-gray-400 line-through">₹<?= formatPrice($sellingPrice); ?></span>
                <?php else: ?>
                    <span class="text-2xl font-bold text-[#C6A769]">₹<?= formatPrice($sellingPrice); ?></span>
                <?php endif; ?>
            </div>

            <div class="mt-6 flex gap-3">
                <a href="<?= BASE_URL ?>product-details.php?id=<?= (int) $product['id']; ?>"
                    class="flex-1 rounded-full border border-black py-3 text-center font-semibold transition hover:bg-black hover:text-white">
                    View
                </a>

                <button class="flex-1 rounded-full bg-black py-3 text-white font-semibold transition hover:bg-[#C6A769] hover:text-black btn-shimmer">
                    Add To Cart
                </button>
            </div>

        </div>

    </div>
    <?php
}
?>

<!-- ============================================================
     BEST SELLING PRODUCTS
============================================================ -->

<section class="bg-white section-spacing" data-aos="fade-up" data-aos-duration="800">

    <div class="container mx-auto px-6">

        <div class="flex items-end justify-between mb-14" data-aos="fade-up" data-aos-duration="700">
            <div>
                <span class="uppercase tracking-[4px] text-[#C6A769] text-sm">Featured</span>
                <h2 class="mt-3 text-4xl lg:text-5xl font-serif font-semibold">Best Selling Products</h2>
            </div>
            <a href="<?= BASE_URL ?>shop.php" class="font-semibold hover:text-[#C6A769] transition">View All →</a>
        </div>

        <?php if (!empty($bestSelling)): ?>

            <div class="bestSellerSwiper swiper" data-aos="fade-up" data-aos-duration="700" data-aos-delay="100">

                <div class="swiper-wrapper">

                    <?php foreach ($bestSelling as $index => $product): ?>
                        <div class="swiper-slide">
                            <?php renderProductCard($product, $index, $database); ?>
                        </div>
                    <?php endforeach; ?>

                </div>

                <div class="swiper-pagination"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>

            </div>

        <?php else: ?>

            <div class="py-20 text-center">
                <h3 class="text-2xl font-semibold">No Best Selling Products Found</h3>
            </div>

        <?php endif; ?>

    </div>

</section>

<!-- ============================================================
     CUSTOMER FAVORITES
============================================================ -->

<section class="bg-[#F8F6F1] section-spacing" data-aos="fade-up" data-aos-duration="800">

    <div class="container mx-auto px-6">

        <div class="flex justify-between items-end mb-14" data-aos="fade-up" data-aos-duration="700">
            <div>
                <span class="uppercase tracking-[4px] text-[#C6A769] text-sm">Customer Choice</span>
                <h2 class="mt-3 text-4xl lg:text-5xl font-serif font-semibold">Customer Favorites</h2>
            </div>
            <a href="<?= BASE_URL ?>shop.php" class="font-semibold hover:text-[#C6A769]">View All →</a>
        </div>

        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">

            <?php if (!empty($customerFavorite)): ?>
                <?php foreach ($customerFavorite as $index => $product): ?>
                    <?php renderProductCard($product, $index, $database); ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-4 py-20 text-center">
                    <h3 class="text-2xl font-semibold">No Customer Favorites Found</h3>
                </div>
            <?php endif; ?>

        </div>

    </div>

</section>

<!-- ============================================================
     WHY CHOOSE US
============================================================ -->

<section class="bg-[#080808] section-spacing" data-aos="fade-up" data-aos-duration="800">

    <div class="container mx-auto px-6">

        <div class="text-center" data-aos="fade-up" data-aos-duration="700">
            <span class="uppercase tracking-[4px] text-[#C6A769]">Why Choose Us</span>
            <h2 class="mt-4 text-5xl font-serif text-white">Luxury Meets Quality</h2>
        </div>

        <div class="grid gap-8 mt-16 md:grid-cols-2 lg:grid-cols-4">

            <?php
            $features = [
                ['icon' => 'fa-gem', 'title' => 'Premium Quality', 'desc' => 'Every product is crafted with premium materials and exceptional finishing.'],
                ['icon' => 'fa-truck-fast', 'title' => 'Fast Delivery', 'desc' => 'Quick and secure shipping across India.'],
                ['icon' => 'fa-arrow-rotate-left', 'title' => 'Easy Returns', 'desc' => 'Hassle-free returns with customer-first support.'],
                ['icon' => 'fa-lock', 'title' => 'Secure Payment', 'desc' => '100% secure payment gateway for every transaction.'],
            ];

            foreach ($features as $index => $feature):
            ?>

                <div class="rounded-3xl border border-white/10 bg-white/5 backdrop-blur-md p-8 text-center transition duration-500 hover:border-[#C6A769] hover:-translate-y-2"
                    data-aos="fade-up" data-aos-duration="700" data-aos-delay="<?= $index * 100 ?>">

                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-[#C6A769] text-3xl text-black transition duration-500 group-hover:rotate-12">
                        <i class="fa-solid <?= htmlspecialchars($feature['icon']); ?>"></i>
                    </div>

                    <h3 class="mt-6 text-2xl font-serif text-white"><?= htmlspecialchars($feature['title']); ?></h3>
                    <p class="mt-4 text-gray-400"><?= htmlspecialchars($feature['desc']); ?></p>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

</section>

<!-- ============================================================
     NEW ARRIVALS
============================================================ -->

<section class="bg-[#F8F6F1] section-spacing" data-aos="fade-up" data-aos-duration="800">

    <div class="container mx-auto px-6">

        <div class="text-center mb-14" data-aos="fade-up" data-aos-duration="700">
            <span class="uppercase tracking-[4px] text-[#C6A769] text-sm">New Arrivals</span>
            <h2 class="mt-3 text-4xl lg:text-5xl font-serif font-semibold">Fresh Collection</h2>
            <p class="mt-4 text-gray-500 max-w-2xl mx-auto">
                Discover our latest arrivals featuring premium quality and modern designs.
            </p>
        </div>

        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">

            <?php if (!empty($newArrival)): ?>
                <?php foreach ($newArrival as $index => $product): ?>
                    <?php renderProductCard($product, $index, $database, ['label' => 'NEW', 'class' => 'bg-[#C6A769] text-black']); ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-4 py-16 text-center">
                    <h3 class="text-2xl font-semibold text-gray-700">No New Arrival Products Found</h3>
                </div>
            <?php endif; ?>

        </div>

    </div>

</section>

<!-- ============================================================
     TRENDING PRODUCTS
============================================================ -->

<section class="bg-white section-spacing" data-aos="fade-up" data-aos-duration="800">

    <div class="container mx-auto px-6">

        <div class="text-center mb-14" data-aos="fade-up" data-aos-duration="700">
            <span class="uppercase tracking-[4px] text-[#C6A769]">Trending</span>
            <h2 class="mt-3 text-4xl lg:text-5xl font-serif font-semibold">Trending Products</h2>
            <p class="mt-4 text-gray-500 max-w-2xl mx-auto">
                Stay ahead of the curve with our most popular trending products.
            </p>
        </div>

        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">

            <?php if (!empty($trendingProducts)): ?>
                <?php foreach ($trendingProducts as $index => $product): ?>
                    <?php renderProductCard($product, $index, $database, ['label' => 'HOT', 'class' => 'bg-red-600 text-white'], 'bg-[#F8F6F1]'); ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-4 py-20 text-center">
                    <h3 class="text-2xl font-semibold">No Trending Products Found</h3>
                </div>
            <?php endif; ?>

        </div>

    </div>

</section>

<!-- ============================================================
     TESTIMONIALS
============================================================ -->

<section class="bg-[#F8F6F1] section-spacing" data-aos="fade-up" data-aos-duration="800">

    <div class="container mx-auto px-6">

        <div class="text-center mb-14" data-aos="fade-up" data-aos-duration="700">
            <span class="uppercase tracking-[4px] text-[#C6A769] text-sm">Testimonials</span>
            <h2 class="mt-3 text-4xl lg:text-5xl font-serif font-semibold">What Our Customers Say</h2>
            <p class="mt-4 text-gray-500 max-w-2xl mx-auto">
                Real experiences from customers who shopped our premium collection.
            </p>
        </div>

        <?php
        // TODO: replace with a `testimonials` table query once available,
        // e.g. $testimonials = $database->select("testimonials", "*", ["status" => "Active"]);
        $testimonials = [
            [
                'name'   => 'Ananya Sharma',
                'role'   => 'Verified Buyer',
                'rating' => 5,
                'text'   => 'The quality of the fabric is outstanding and the delivery was super fast. Definitely shopping here again.',
                'avatar' => 'https://randomuser.me/api/portraits/women/68.jpg',
            ],
            [
                'name'   => 'Rohan Mehta',
                'role'   => 'Verified Buyer',
                'rating' => 5,
                'text'   => 'Loved the fit and finishing of my order. Packaging felt premium right from the moment it arrived.',
                'avatar' => 'https://randomuser.me/api/portraits/men/32.jpg',
            ],
            [
                'name'   => 'Priya Nair',
                'role'   => 'Verified Buyer',
                'rating' => 4,
                'text'   => 'Great collection with genuinely trendy designs. Customer support was quick to help with sizing.',
                'avatar' => 'https://randomuser.me/api/portraits/women/44.jpg',
            ],
            [
                'name'   => 'Karan Verma',
                'role'   => 'Verified Buyer',
                'rating' => 5,
                'text'   => 'Easily the best online store I have shopped from this year. Returns were hassle-free too.',
                'avatar' => 'https://randomuser.me/api/portraits/men/56.jpg',
            ],
        ];
        ?>

        <div class="testimonialSwiper swiper" data-aos="fade-up" data-aos-duration="700" data-aos-delay="100">

            <div class="swiper-wrapper">

                <?php foreach ($testimonials as $t): ?>
                    <div class="swiper-slide">
                        <div class="testimonial-card">

                            <div class="stars mb-4">
                                <?php for ($s = 0; $s < 5; $s++): ?>
                                    <i class="fa-solid fa-star<?= $s < (int) $t['rating'] ? '' : '-half-stroke'; ?>" style="<?= $s >= (int) $t['rating'] ? 'color:#e5e7eb;' : ''; ?>"></i>
                                <?php endfor; ?>
                            </div>

                            <p class="text-gray-600 leading-relaxed flex-1">
                                "<?= htmlspecialchars($t['text']); ?>"
                            </p>

                            <div class="mt-6 flex items-center gap-4">
                                <img src="<?= htmlspecialchars($t['avatar']); ?>" alt="<?= htmlspecialchars($t['name']); ?>" class="avatar" loading="lazy">
                                <div>
                                    <p class="font-semibold"><?= htmlspecialchars($t['name']); ?></p>
                                    <p class="text-sm text-gray-500"><?= htmlspecialchars($t['role']); ?></p>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>

            </div>

            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>

        </div>

    </div>

</section>

<!-- ============================================================
     NEWSLETTER SECTION
============================================================ -->

<section class="relative overflow-hidden section-spacing" data-aos="fade-up" data-aos-duration="800">

    <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?w=1920&q=80"
            class="h-full w-full object-cover" loading="lazy" alt="Newsletter background">
        <div class="absolute inset-0 bg-black/75"></div>
    </div>

    <div class="relative container mx-auto px-6 text-center">

        <span class="uppercase tracking-[5px] text-[#C6A769]" data-aos="fade-up" data-aos-duration="600">Newsletter</span>

        <h2 class="mt-5 text-4xl lg:text-6xl text-white font-serif" data-aos="fade-up" data-aos-duration="700" data-aos-delay="100">
            Stay Updated
        </h2>

        <p class="mt-6 max-w-2xl mx-auto text-gray-300 text-lg" data-aos="fade-up" data-aos-duration="700" data-aos-delay="200">
            Subscribe and receive exclusive offers, new arrivals and premium fashion updates.
        </p>

        <form class="mx-auto mt-10 flex max-w-xl flex-col gap-4 sm:flex-row" data-aos="fade-up" data-aos-duration="700" data-aos-delay="300">

            <input type="email" placeholder="Enter your email" required
                class="newsletter-input flex-1 rounded-full px-6 py-4 outline-none focus:ring-2 focus:ring-[#C6A769]">

            <button type="submit" class="rounded-full bg-[#C6A769] px-8 py-4 font-semibold hover:bg-white transition btn-shimmer">
                Subscribe
            </button>

        </form>

    </div>

</section>

<!-- Back to top button -->
<button id="backToTop" aria-label="Back to top">
    <i class="fa-solid fa-arrow-up"></i>
</button>

<?php include 'includes/footer.php'; ?>

<!-- ============================================================
     SCRIPTS
============================================================ -->

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true,
            offset: 120,
            easing: 'ease-in-out',
            delay: 0
        });

        setTimeout(function() {
            AOS.refresh();
        }, 500);

        // Hero Banner Swiper
        new Swiper('.hero-swiper', {
            loop: true,
            speed: 1000,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
                dynamicBullets: true,
            },
            effect: 'fade',
            fadeEffect: { crossFade: true },
            on: {
                slideChangeTransitionEnd: function() {
                    AOS.refresh();
                }
            }
        });

        // Best Seller Swiper
        if (document.querySelector('.bestSellerSwiper')) {
            new Swiper(".bestSellerSwiper", {
                slidesPerView: 1,
                spaceBetween: 30,
                loop: true,
                autoplay: {
                    delay: 3500,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true
                },
                pagination: {
                    el: '.bestSellerSwiper .swiper-pagination',
                    clickable: true
                },
                navigation: {
                    nextEl: '.bestSellerSwiper .swiper-button-next',
                    prevEl: '.bestSellerSwiper .swiper-button-prev',
                },
                breakpoints: {
                    640: { slidesPerView: 2 },
                    1024: { slidesPerView: 4 }
                }
            });
        }

        // Testimonial Swiper
        if (document.querySelector('.testimonialSwiper')) {
            new Swiper(".testimonialSwiper", {
                loop: true,
                spaceBetween: 30,
                autoplay: {
                    delay: 4000,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true
                },
                pagination: {
                    el: '.testimonialSwiper .swiper-pagination',
                    clickable: true
                },
                navigation: {
                    nextEl: '.testimonialSwiper .swiper-button-next',
                    prevEl: '.testimonialSwiper .swiper-button-prev',
                },
                breakpoints: {
                    768: { slidesPerView: 2 },
                    1024: { slidesPerView: 3 }
                }
            });
        }

        // ---- Animated stat counters (Products / Categories / Quality) ----
        const counters = document.querySelectorAll('.counter');

        const animateCounter = (el) => {
            const target = parseInt(el.dataset.target, 10) || 0;
            const suffix = el.dataset.suffix || '+';
            const duration = 1500;
            const startTime = performance.now();

            const step = (now) => {
                const progress = Math.min((now - startTime) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3); // ease-out cubic
                el.textContent = Math.floor(eased * target) + (progress >= 1 ? suffix : '');
                if (progress < 1) {
                    requestAnimationFrame(step);
                } else {
                    el.textContent = target + suffix;
                }
            };
            requestAnimationFrame(step);
        };

        if (counters.length) {
            const counterObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounter(entry.target);
                        counterObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.4 });

            counters.forEach(el => counterObserver.observe(el));
        }

        // ---- Back to top button ----
        const backToTop = document.getElementById('backToTop');

        if (backToTop) {
            window.addEventListener('scroll', function() {
                if (window.scrollY > 500) {
                    backToTop.classList.add('show');
                } else {
                    backToTop.classList.remove('show');
                }
            });

            backToTop.addEventListener('click', function() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }

    });
</script>