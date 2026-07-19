<?php
session_start();

require_once 'config/constant.php';
require_once 'config/db.php';

$page_title = "About Us";

$totalProducts   = $database->count("products");
$totalCategories = $database->count("categories");

include 'includes/header.php';
?>

<!-- ============================================================
     AOS LIBRARY CDN
============================================================ -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<style>
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

    [data-aos] {
        opacity: 0;
        transition-property: opacity, transform;
    }

    [data-aos].aos-animate {
        opacity: 1;
    }

    /* ---------- About Hero ---------- */
    .about-hero {
        position: relative;
        min-height: 55vh;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: #080808;
    }

    .about-hero img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.45;
        transform: scale(1.08);
        transition: transform 6s ease-out;
    }

    .about-hero:hover img,
    .about-hero.in-view img {
        transform: scale(1);
    }

    .about-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(to bottom, rgba(0,0,0,0.55), rgba(0,0,0,0.85));
    }

    .about-hero-content {
        position: relative;
        z-index: 2;
        text-align: center;
        padding: 0 1.5rem;
    }

    .about-hero .badge {
        display: contents;
        align-items: center;
        gap: 0.5rem;
        border: 1px solid #C6A769;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(4px);
        padding: 0.5rem 1.25rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        color: #E7D3A8;
        margin-bottom: 1.25rem;
    }

    .about-hero h1 {
        font-size: 3.5rem;
        color: #fff;
        line-height: 1.15;
    }

    .about-hero p {
        margin-top: 1rem;
        color: #d1d5db;
        max-width: 640px;
        margin-left: auto;
        margin-right: auto;
        font-size: 1.1rem;
    }

    @media (max-width: 768px) {
        .about-hero h1 { font-size: 2.4rem; }
    }

    /* ---------- Story image frame (SMALLER, right-aligned) ---------- */
    .story-photo-frame {
        position: relative;
        border-radius: 1.5rem;
        overflow: hidden;
        box-shadow: 0 20px 45px rgba(0,0,0,0.16);
        width: 100%;
        max-width: 340px;
        aspect-ratio: 3 / 4;
        margin-left: auto;
        margin-right: auto;
    }

    @media (min-width: 1024px) {
        .story-photo-frame {
            margin-right: 0;
        }
    }

    .story-photo-frame img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.8s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .story-photo-frame:hover img {
        transform: scale(1.06);
    }

    .story-photo-frame::after {
        content: "";
        position: absolute;
        inset: 0;
        border: 1px solid rgba(198, 167, 105, 0.5);
        border-radius: 1.5rem;
        margin: 10px;
        pointer-events: none;
    }

    .story-caption {
        position: absolute;
        left: 18px;
        bottom: 18px;
        right: 18px;
        background: rgba(8, 8, 8, 0.75);
        backdrop-filter: blur(6px);
        color: #fff;
        padding: 0.75rem 1.1rem;
        border-radius: 0.9rem;
        border-left: 3px solid #C6A769;
    }

    .story-caption .name {
        font-family: serif;
        font-size: 1rem;
        font-weight: 600;
    }

    .story-caption .role {
        font-size: 0.7rem;
        color: #C6A769;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-top: 2px;
    }

    /* ---------- Value cards ---------- */
    .value-card {
        border-radius: 1.75rem;
        padding: 2.5rem 2rem;
        background: #ffffff;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        transition: transform 0.5s ease, box-shadow 0.5s ease;
        text-align: center;
    }

    .value-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 25px 45px rgba(0,0,0,0.1);
    }

    .value-card .icon-circle {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: #C6A769;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        color: #000;
        margin: 0 auto 1.5rem;
        transition: transform 0.5s ease;
    }

    .value-card:hover .icon-circle {
        transform: rotate(12deg) scale(1.08);
    }

    /* ---------- Mission / Vision cards ---------- */
    .mv-card {
        border-radius: 1.75rem;
        padding: 3rem 2.5rem;
        position: relative;
        overflow: hidden;
        transition: transform 0.5s ease;
    }

    .mv-card:hover {
        transform: translateY(-6px);
    }

    .mv-card .mv-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        margin-bottom: 1.5rem;
    }

    .mv-card.dark {
        background: #080808;
        color: #fff;
    }

    .mv-card.dark .mv-icon {
        background: #C6A769;
        color: #000;
    }

    .mv-card.light {
        background: #F8F6F1;
        color: #111;
    }

    .mv-card.light .mv-icon {
        background: #080808;
        color: #C6A769;
    }

    /* ---------- Process / Craftsmanship timeline ---------- */
    .process-track {
        position: relative;
    }

    .process-track::before {
        content: "";
        position: absolute;
        top: 34px;
        left: 0;
        right: 0;
        height: 2px;
        background: repeating-linear-gradient(to right, #C6A769 0 10px, transparent 10px 20px);
        display: none;
    }

    @media (min-width: 1024px) {
        .process-track::before { display: block; }
    }

    .process-step {
        position: relative;
        text-align: center;
    }

    .process-step .step-number {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid #C6A769;
        color: #C6A769;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        font-weight: 700;
        margin: 0 auto 1.25rem;
        position: relative;
        z-index: 2;
        transition: all 0.4s ease;
    }

    .process-step:hover .step-number {
        background: #C6A769;
        color: #000;
        transform: scale(1.08);
    }

    /* ---------- Sustainability split ---------- */
    .eco-badge-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }

    .eco-badge {
        border-radius: 1.25rem;
        background: #fff;
        padding: 1.5rem;
        text-align: center;
        box-shadow: 0 8px 24px rgba(0,0,0,0.05);
        transition: transform 0.4s ease;
    }

    .eco-badge:hover {
        transform: translateY(-5px);
    }

    .eco-badge i {
        font-size: 1.6rem;
        color: #C6A769;
        margin-bottom: 0.6rem;
    }

    /* ---------- CTA ---------- */
    .about-cta {
        position: relative;
        overflow: hidden;
        border-radius: 2rem;
        background: #080808;
        padding: 4rem 2rem;
        text-align: center;
    }

    .about-cta::before {
        content: "";
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 30% 30%, rgba(198,167,105,0.18), transparent 60%);
    }
</style>

<!-- ============================================================
     ABOUT HERO
============================================================ -->

<section class="about-hero">

    <img src="https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=1600&q=80" alt="DARREN & Co Story">

    <div class="about-hero-content">

        <span class="badge" data-aos="fade-up" data-aos-duration="700">
            <i class="fa-solid fa-heart"></i>
            Our Story
        </span>

        <h1 class="font-serif" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
            About DARREN &amp; Co
        </h1>

        <p data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
            A name close to our heart. A promise woven into every thread.
        </p>

    </div>

</section>

<!-- ============================================================
     OUR STORY (Content LEFT, Photo RIGHT — smaller frame)
============================================================ -->

<section class="bg-white section-spacing">

    <div class="container mx-auto px-6">

        <div class="grid lg:grid-cols-5 gap-12 lg:gap-16 items-center">

            <!-- Text (wider column, comes first = left on desktop) -->
            <div class="lg:col-span-3" data-aos="fade-right" data-aos-duration="900">

                <span class="uppercase tracking-[4px] text-[#C6A769] text-sm">Why We Began</span>

                <h2 class="mt-3 text-4xl lg:text-5xl font-serif font-semibold">
                    A Name Close To Our Heart
                </h2>

                <div class="mt-6 space-y-5 text-gray-600 leading-relaxed text-lg">

                    <p>
                        When we first dreamed of starting a company, we knew we wanted it to stand
                        for something closer to our heart than just business. We wanted it to
                        reflect passion, growth, and a promise of quality.
                    </p>

                    <p>
                        That's why <strong class="text-black">DARREN &amp; Co</strong> is proudly
                        named after our son, <strong class="text-black">Darren Daris Neyyan</strong>.
                    </p>

                    <p>
                        We believe that fashion shouldn't demand a compromise between looking
                        effortless and feeling completely at ease. That's why we created
                        DARREN &amp; Co — a modern western wear line crafted exclusively from pure,
                        breathable cotton.
                    </p>

                </div>

            </div>

            <!-- Photo (narrower column, smaller frame, right on desktop) -->
            <div class="lg:col-span-2" data-aos="fade-left" data-aos-duration="900" data-aos-delay="100">

                <div class="story-photo-frame">

                    <!--
                        Replace with the actual family / founder photo.
                        Current path: assets/images/founder.jpeg
                    -->
                    <img src="<?= BASE_URL ?>assets/images/founder.jpeg"
                        alt="Darren Daris Neyyan"
                        onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1519689680058-324335c77eba?w=900&q=80';">

                    <div class="story-caption">
                        <div class="name">Darren Daris Neyyan</div>
                        <div class="role">The inspiration behind DARREN &amp; Co</div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<!-- ============================================================
     MISSION & VISION
============================================================ -->

<section class="bg-[#F8F6F1] section-spacing">

    <div class="container mx-auto px-6">

        <div class="text-center mb-14" data-aos="fade-up" data-aos-duration="700">
            <span class="uppercase tracking-[4px] text-[#C6A769] text-sm">What Drives Us</span>
            <h2 class="mt-3 text-4xl lg:text-5xl font-serif font-semibold">Our Mission &amp; Vision</h2>
        </div>

        <div class="grid gap-8 lg:grid-cols-2">

            <div class="mv-card dark" data-aos="fade-up" data-aos-duration="700">
                <div class="mv-icon"><i class="fa-solid fa-bullseye"></i></div>
                <h3 class="text-2xl font-serif font-semibold">Our Mission</h3>
                <p class="mt-4 text-gray-300 leading-relaxed">
                    To craft everyday western wear from pure, breathable cotton — so that comfort
                    is never something you have to trade for style. Every stitch is made with the
                    same care we'd want for our own family.
                </p>
            </div>

            <div class="mv-card light" data-aos="fade-up" data-aos-duration="700" data-aos-delay="150">
                <div class="mv-icon"><i class="fa-solid fa-eye"></i></div>
                <h3 class="text-2xl font-serif font-semibold">Our Vision</h3>
                <p class="mt-4 text-gray-600 leading-relaxed">
                    To become the wardrobe people trust for pieces that move with their day — from
                    morning coffee to evening unwinding — while staying true to natural, honest
                    fabric.
                </p>
            </div>

        </div>

    </div>

</section>

<!-- ============================================================
     WHAT WE STAND FOR
============================================================ -->

<section class="bg-white section-spacing">

    <div class="container mx-auto px-6">

        <div class="text-center mb-16" data-aos="fade-up" data-aos-duration="700">

            <span class="uppercase tracking-[4px] text-[#C6A769] text-sm">Our Philosophy</span>

            <h2 class="mt-3 text-4xl lg:text-5xl font-serif font-semibold">
                Designed To Move With You
            </h2>

            <p class="mt-5 text-gray-600 max-w-3xl mx-auto text-lg leading-relaxed">
                Designed for the fast pace of modern life, our pieces bring together classic
                Western silhouettes with the unmatched, skin-loving comfort of natural fibers —
                just beautifully structured, airy, and versatile outfits that move with you from
                morning coffee to evening unwinding.
            </p>

        </div>

        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">

            <?php
            $values = [
                [
                    'icon'  => 'fa-leaf',
                    'title' => 'Pure Cotton',
                    'desc'  => 'Crafted exclusively from breathable, natural cotton that feels as good as it looks.'
                ],
                [
                    'icon'  => 'fa-star',
                    'title' => 'Timeless Style',
                    'desc'  => 'Classic Western silhouettes reimagined for the way you actually live today.'
                ],
                [
                    'icon'  => 'fa-hand-holding-heart',
                    'title' => 'Mindful Quality',
                    'desc'  => 'Every piece is made with intention — quality that respects you and the process.'
                ],
            ];

            foreach ($values as $index => $value):
            ?>

                <div class="value-card" data-aos="fade-up" data-aos-duration="700" data-aos-delay="<?= $index * 120 ?>">

                    <div class="icon-circle">
                        <i class="fa-solid <?= htmlspecialchars($value['icon']); ?>"></i>
                    </div>

                    <h3 class="text-2xl font-serif font-semibold"><?= htmlspecialchars($value['title']); ?></h3>

                    <p class="mt-3 text-gray-500 leading-relaxed"><?= htmlspecialchars($value['desc']); ?></p>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

</section>

<!-- ============================================================
     CRAFTSMANSHIP PROCESS
============================================================ -->

<section class="bg-[#F8F6F1] section-spacing">

    <div class="container mx-auto px-6">

        <div class="text-center mb-16" data-aos="fade-up" data-aos-duration="700">
            <span class="uppercase tracking-[4px] text-[#C6A769] text-sm">From Fabric To Finish</span>
            <h2 class="mt-3 text-4xl lg:text-5xl font-serif font-semibold">Our Craftsmanship</h2>
            <p class="mt-5 text-gray-600 max-w-2xl mx-auto text-lg">
                Every piece passes through hands that care, before it ever reaches your wardrobe.
            </p>
        </div>

        <div class="process-track grid gap-10 md:grid-cols-2 lg:grid-cols-4">

            <?php
            $process = [
                ['num' => '01', 'title' => 'Sourcing Cotton',   'desc' => 'We select pure, breathable cotton grown responsibly.'],
                ['num' => '02', 'title' => 'Cut &amp; Stitch',  'desc' => 'Skilled hands shape each silhouette with precision.'],
                ['num' => '03', 'title' => 'Quality Check',     'desc' => 'Every seam and fit is inspected before it moves on.'],
                ['num' => '04', 'title' => 'Packed With Care',  'desc' => 'Folded, wrapped and shipped straight to your door.'],
            ];

            foreach ($process as $index => $step):
            ?>

                <div class="process-step" data-aos="fade-up" data-aos-duration="700" data-aos-delay="<?= $index * 120 ?>">
                    <div class="step-number"><?= htmlspecialchars($step['num']); ?></div>
                    <h3 class="text-xl font-serif font-semibold"><?= $step['title']; ?></h3>
                    <p class="mt-2 text-gray-500 text-sm leading-relaxed"><?= htmlspecialchars($step['desc']); ?></p>
                </div>

            <?php endforeach; ?>

        </div>

    </div>

</section>

<!-- ============================================================
     SUSTAINABILITY / COMFORT PROMISE
============================================================ -->

<section class="bg-white section-spacing">

    <div class="container mx-auto px-6">

        <div class="grid lg:grid-cols-2 gap-16 items-center">

            <div data-aos="fade-right" data-aos-duration="800">
                <span class="uppercase tracking-[4px] text-[#C6A769] text-sm">Our Promise</span>
                <h2 class="mt-3 text-4xl lg:text-5xl font-serif font-semibold">
                    Comfort You Can Trust
                </h2>
                <p class="mt-5 text-gray-600 text-lg leading-relaxed">
                    Cotton isn't just a fabric choice for us — it's a promise. Breathable,
                    skin-friendly, and gentle on the planet, every DARREN &amp; Co piece is made
                    to be worn, loved and lived in.
                </p>

                <div class="eco-badge-grid mt-8">
                    <div class="eco-badge">
                        <i class="fa-solid fa-seedling"></i>
                        <p class="font-semibold text-sm mt-1">100% Cotton</p>
                    </div>
                    <div class="eco-badge">
                        <i class="fa-solid fa-wind"></i>
                        <p class="font-semibold text-sm mt-1">Breathable Fit</p>
                    </div>
                    <div class="eco-badge">
                        <i class="fa-solid fa-hand-sparkles"></i>
                        <p class="font-semibold text-sm mt-1">Skin Friendly</p>
                    </div>
                    <div class="eco-badge">
                        <i class="fa-solid fa-recycle"></i>
                        <p class="font-semibold text-sm mt-1">Responsibly Made</p>
                    </div>
                </div>
            </div>

            <div data-aos="fade-left" data-aos-duration="800" data-aos-delay="100">
                <img src="https://images.unsplash.com/photo-1445205170230-053b83016050?w=900&q=80"
                    alt="Pure cotton fabric" loading="lazy"
                    class="w-full h-[420px] object-cover rounded-3xl shadow-2xl">
            </div>

        </div>

    </div>

</section>

<!-- ============================================================
     WELCOME STRIP
============================================================ -->

<section class="bg-[#F8F6F1] section-spacing">

    <div class="container mx-auto px-6 text-center max-w-3xl">

        <div data-aos="zoom-in" data-aos-duration="800">

            <span class="uppercase tracking-[4px] text-[#C6A769] text-sm">Welcome Home</span>

            <h2 class="mt-3 text-3xl lg:text-4xl font-serif font-semibold leading-snug">
                We design for those who value mindful quality, timeless style, and the pure,
                simple luxury of cotton.
            </h2>

            <p class="mt-6 text-gray-500 text-lg">
                Welcome to your wardrobe's new happy place.
            </p>

        </div>

    </div>

</section>

<!-- ============================================================
     STATS STRIP
============================================================ -->

<section class="bg-[#080808] section-spacing">

    <div class="container mx-auto px-6">

        <div class="grid grid-cols-2 md:grid-cols-3 gap-8 text-center">

            <div data-aos="fade-up" data-aos-duration="700">
                <div class="text-4xl font-bold text-[#C6A769]"><?= (int) $totalProducts; ?>+</div>
                <div class="mt-2 text-gray-400 uppercase tracking-widest text-sm">Products</div>
            </div>

            <div data-aos="fade-up" data-aos-duration="700" data-aos-delay="100">
                <div class="text-4xl font-bold text-[#C6A769]"><?= (int) $totalCategories; ?>+</div>
                <div class="mt-2 text-gray-400 uppercase tracking-widest text-sm">Categories</div>
            </div>

            <div class="col-span-2 md:col-span-1" data-aos="fade-up" data-aos-duration="700" data-aos-delay="200">
                <div class="text-4xl font-bold text-[#C6A769]">100%</div>
                <div class="mt-2 text-gray-400 uppercase tracking-widest text-sm">Pure Cotton</div>
            </div>

        </div>

    </div>

</section>

<!-- ============================================================
     CTA
============================================================ -->

<section class="section-spacing bg-white">

    <div class="container mx-auto px-6">

        <div class="about-cta" data-aos="fade-up" data-aos-duration="800">

            <div class="relative z-10">

                <h2 class="text-3xl lg:text-4xl font-serif text-white">
                    Discover The DARREN &amp; Co Collection
                </h2>

                <p class="mt-4 text-gray-400 max-w-xl mx-auto">
                    Explore pieces crafted for comfort, made to move with your day.
                </p>

                <a href="<?= BASE_URL ?>shop.php"
                    class="mt-8 inline-block rounded-full bg-[#C6A769] text-black px-10 py-4 font-semibold transition hover:bg-white hover:scale-105">
                    Shop The Collection
                </a>

            </div>

        </div>

    </div>

</section>

<?php include 'includes/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        AOS.init({
            duration: 800,
            once: true,
            offset: 100,
            easing: 'ease-in-out'
        });
    });
</script>