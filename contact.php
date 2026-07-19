<?php
session_start();

require_once 'config/constant.php';
require_once 'config/db.php';

$page_title = "Contact Us";

/* ==========================================
   FORM HANDLING
========================================== */
$formErrors  = [];
$formSuccess = false;
$old = [
    'name'    => '',
    'email'   => '',
    'phone'   => '',
    'subject' => '',
    'message' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {

    $old['name']    = trim($_POST['name'] ?? '');
    $old['email']   = trim($_POST['email'] ?? '');
    $old['phone']   = trim($_POST['phone'] ?? '');
    $old['subject'] = trim($_POST['subject'] ?? '');
    $old['message'] = trim($_POST['message'] ?? '');

    // Validation
    if ($old['name'] === '') {
        $formErrors['name'] = 'Please enter your name.';
    }

    if ($old['email'] === '') {
        $formErrors['email'] = 'Please enter your email.';
    } elseif (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $formErrors['email'] = 'Please enter a valid email address.';
    }

    if ($old['phone'] !== '' && !preg_match('/^[0-9+\-\s()]{7,20}$/', $old['phone'])) {
        $formErrors['phone'] = 'Please enter a valid phone number.';
    }

    if ($old['subject'] === '') {
        $formErrors['subject'] = 'Please enter a subject.';
    }

    if ($old['message'] === '') {
        $formErrors['message'] = 'Please write your message.';
    } elseif (strlen($old['message']) < 10) {
        $formErrors['message'] = 'Message should be at least 10 characters.';
    }

    if (empty($formErrors)) {

        try {
           
            $database->insert("contact_messages", [
                "name"       => $old['name'],
                "email"      => $old['email'],
                "phone"      => $old['phone'],
                "subject"    => $old['subject'],
                "message"    => $old['message'],
                "created_at" => date('Y-m-d H:i:s'),
            ]);

            $formSuccess = true;
            $old = ['name' => '', 'email' => '', 'phone' => '', 'subject' => '', 'message' => ''];

        } catch (Exception $e) {
            $formErrors['general'] = 'Something went wrong while sending your message. Please try again.';
        }
    }

    // ---- AJAX request: respond with JSON, no page reload ----
    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $formSuccess,
            'errors'  => $formErrors,
        ]);
        exit;
    }
}

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

    /* ---------- Contact Hero ---------- */
    .contact-hero {
        position: relative;
        min-height: 42vh;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: #080808;
    }

    .contact-hero img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.4;
        transform: scale(1.08);
        transition: transform 6s ease-out;
    }

    .contact-hero:hover img {
        transform: scale(1);
    }

    .contact-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(to bottom, rgba(0,0,0,0.55), rgba(0,0,0,0.85));
    }

    .contact-hero-content {
        position: relative;
        z-index: 2;
        text-align: center;
        padding: 0 1.5rem;
    }

    .contact-hero .badge {
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

    .contact-hero h1 {
        font-size: 3.25rem;
        color: #fff;
        line-height: 1.15;
    }

    .contact-hero p {
        margin-top: 1rem;
        color: #d1d5db;
        max-width: 560px;
        margin-left: auto;
        margin-right: auto;
        font-size: 1.05rem;
    }

    @media (max-width: 768px) {
        .contact-hero h1 { font-size: 2.2rem; }
    }

    /* ---------- Info cards ---------- */
    .info-card {
        background: #fff;
        border-radius: 1.5rem;
        padding: 2rem;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        transition: transform 0.5s ease, box-shadow 0.5s ease;
        height: 100%;
    }

    .info-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 22px 40px rgba(0,0,0,0.1);
    }

    .info-card .icon-circle {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: #C6A769;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        color: #000;
        margin: 0 auto 1.25rem;
        transition: transform 0.5s ease;
    }

    .info-card:hover .icon-circle {
        transform: rotate(10deg) scale(1.08);
    }

    /* ---------- Form ---------- */
    .contact-form-card {
        background: #fff;
        border-radius: 2rem;
        padding: 2.75rem;
        box-shadow: 0 25px 60px rgba(0,0,0,0.08);
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .form-control {
        width: 100%;
        padding: 0.9rem 1.1rem;
        border: 1.5px solid #e5e7eb;
        border-radius: 0.9rem;
        outline: none;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: #fafafa;
    }

    .form-control:focus {
        border-color: #C6A769;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(198, 167, 105, 0.15);
    }

    .form-control.is-invalid {
        border-color: #dc2626;
        background: #fef2f2;
    }

    .form-error {
        color: #dc2626;
        font-size: 0.8rem;
        margin-top: 0.4rem;
    }

    .form-error:empty {
        display: none;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 140px;
    }

    .btn-submit {
        width: 100%;
        background: #080808;
        color: #fff;
        padding: 1.05rem;
        border-radius: 0.9rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-submit:hover {
        background: #C6A769;
        color: #000;
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(198, 167, 105, 0.35);
    }

    .btn-submit::before {
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

    .btn-submit:hover::before {
        left: 130%;
    }

    .alert-success {
        background: #ecfdf5;
        border: 1px solid #10b981;
        color: #065f46;
        padding: 1rem 1.25rem;
        border-radius: 1rem;
        margin-bottom: 1.75rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .alert-danger {
        background: #fef2f2;
        border: 1px solid #dc2626;
        color: #991b1b;
        padding: 1rem 1.25rem;
        border-radius: 1rem;
        margin-bottom: 1.75rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    /* ---------- Map frame ---------- */
    .map-frame {
        border-radius: 2rem;
        overflow: hidden;
        box-shadow: 0 20px 45px rgba(0,0,0,0.1);
        height: 100%;
        min-height: 320px;
    }

    .map-frame iframe {
        width: 100%;
        height: 100%;
        min-height: 320px;
        border: 0;
        filter: grayscale(15%);
    }

    /* ---------- Social row ---------- */
    .social-row {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
    }

    .social-row a {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #080808;
        color: #C6A769;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .social-row a:hover {
        background: #C6A769;
        color: #000;
        transform: translateY(-4px);
    }
</style>

<!-- ============================================================
     CONTACT HERO
============================================================ -->

<section class="contact-hero">

    <img src="https://images.unsplash.com/photo-1423666639041-f56000c27a9a?w=1600&q=80" alt="Contact DARREN & Co">

    <div class="contact-hero-content">

        <span class="badge" data-aos="fade-up" data-aos-duration="700">
            <i class="fa-solid fa-envelope"></i>
            Get In Touch
        </span>

        <h1 class="font-serif" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
            Contact Us
        </h1>

        <p data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
            Have a question about an order, sizing, or just want to say hello? We'd love to hear from you.
        </p>

    </div>

</section>

<!-- ============================================================
     CONTACT INFO CARDS
============================================================ -->

<section class="bg-[#F8F6F1] section-spacing">

    <div class="container mx-auto px-6">

        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">

            <?php
            $infoCards = [
                ['icon' => 'fa-location-dot', 'title' => 'Visit Us',    'lines' => ['123 Fashion Street', 'Mumbai, Maharashtra, India']],
                ['icon' => 'fa-phone',        'title' => 'Call Us',     'lines' => ['+91 98765 43210', 'Mon - Sat, 10am - 7pm']],
                ['icon' => 'fa-envelope',     'title' => 'Email Us',    'lines' => ['support@darrenandco.com', "We reply within 24 hours"]],
                ['icon' => 'fa-clock',        'title' => 'Working Hours', 'lines' => ['Mon - Sat: 10:00 - 19:00', 'Sunday: Closed']],
            ];

            foreach ($infoCards as $index => $card):
            ?>

                <div class="info-card" data-aos="fade-up" data-aos-duration="700" data-aos-delay="<?= $index * 100 ?>">

                    <div class="icon-circle">
                        <i class="fa-solid <?= htmlspecialchars($card['icon']); ?>"></i>
                    </div>

                    <h3 class="text-lg font-serif font-semibold"><?= htmlspecialchars($card['title']); ?></h3>

                    <?php foreach ($card['lines'] as $line): ?>
                        <p class="mt-1 text-gray-500 text-sm"><?= htmlspecialchars($line); ?></p>
                    <?php endforeach; ?>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

</section>

<!-- ============================================================
     FORM + MAP
============================================================ -->

<section class="bg-white section-spacing">

    <div class="container mx-auto px-6">

        <div class="grid lg:grid-cols-2 gap-12 items-stretch">

            <!-- Contact Form -->
            <div data-aos="fade-right" data-aos-duration="800">

                <div class="contact-form-card">

                    <span class="uppercase tracking-[4px] text-[#C6A769] text-sm">Send A Message</span>
                    <h2 class="mt-2 text-3xl lg:text-4xl font-serif font-semibold">We'd Love To Hear From You</h2>

                    <div id="formAlertBox">
                        <?php if ($formSuccess): ?>
                            <div class="alert-success mt-6">
                                <i class="fa-solid fa-circle-check text-lg"></i>
                                <span>Thank you! Your message has been sent. We'll get back to you soon.</span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($formErrors['general'])): ?>
                            <div class="alert-danger mt-6">
                                <i class="fa-solid fa-circle-exclamation text-lg"></i>
                                <span><?= htmlspecialchars($formErrors['general']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <form method="POST" action="" class="mt-6" id="contactForm" novalidate>

                        <div class="grid sm:grid-cols-2 gap-x-4">

                            <div class="form-group">
                                <label class="form-label" for="name">Full Name</label>
                                <input type="text" id="name" name="name" placeholder="Your name"
                                    class="form-control <?= isset($formErrors['name']) ? 'is-invalid' : ''; ?>"
                                    value="<?= htmlspecialchars($old['name']); ?>">
                                <p class="form-error" id="error-name"><?= htmlspecialchars($formErrors['name'] ?? ''); ?></p>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="email">Email Address</label>
                                <input type="email" id="email" name="email" placeholder="you@example.com"
                                    class="form-control <?= isset($formErrors['email']) ? 'is-invalid' : ''; ?>"
                                    value="<?= htmlspecialchars($old['email']); ?>">
                                <p class="form-error" id="error-email"><?= htmlspecialchars($formErrors['email'] ?? ''); ?></p>
                            </div>

                        </div>

                        <div class="grid sm:grid-cols-2 gap-x-4">

                            <div class="form-group">
                                <label class="form-label" for="phone">Phone Number <span class="text-gray-400 font-normal">(optional)</span></label>
                                <input type="text" id="phone" name="phone" placeholder="+91 00000 00000"
                                    class="form-control <?= isset($formErrors['phone']) ? 'is-invalid' : ''; ?>"
                                    value="<?= htmlspecialchars($old['phone']); ?>">
                                <p class="form-error" id="error-phone"><?= htmlspecialchars($formErrors['phone'] ?? ''); ?></p>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="subject">Subject</label>
                                <input type="text" id="subject" name="subject" placeholder="How can we help?"
                                    class="form-control <?= isset($formErrors['subject']) ? 'is-invalid' : ''; ?>"
                                    value="<?= htmlspecialchars($old['subject']); ?>">
                                <p class="form-error" id="error-subject"><?= htmlspecialchars($formErrors['subject'] ?? ''); ?></p>
                            </div>

                        </div>

                        <div class="form-group">
                            <label class="form-label" for="message">Message</label>
                            <textarea id="message" name="message" placeholder="Write your message here..."
                                class="form-control <?= isset($formErrors['message']) ? 'is-invalid' : ''; ?>"><?= htmlspecialchars($old['message']); ?></textarea>
                            <p class="form-error" id="error-message"><?= htmlspecialchars($formErrors['message'] ?? ''); ?></p>
                        </div>

                        <button type="submit" name="contact_submit" value="1" class="btn-submit" id="submitBtn">
                            <span id="submitBtnText">Send Message</span>
                        </button>

                    </form>

                </div>

            </div>

            <!-- Map + Social -->
            <div data-aos="fade-left" data-aos-duration="800" data-aos-delay="100" class="flex flex-col">

                <div class="map-frame flex-1">
                    <iframe
                        src="https://www.google.com/maps?q=Mumbai,Maharashtra,India&output=embed"
                        allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>

                <div class="mt-6">
                    <h3 class="text-xl font-serif font-semibold">Follow Us</h3>
                    <p class="text-gray-500 mt-1">Stay in touch for new arrivals and exclusive offers.</p>

                    <div class="social-row">
                        <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                        <a href="#" aria-label="Pinterest"><i class="fa-brands fa-pinterest-p"></i></a>
                    </div>
                </div>

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

        <?php if ($formSuccess): ?>
        // Smooth scroll to the success message (non-JS / first load fallback)
        const successBox = document.querySelector('.alert-success');
        if (successBox) {
            successBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        <?php endif; ?>

        // ---- AJAX contact form submission (no page reload) ----
        const form = document.getElementById('contactForm');
        const alertBox = document.getElementById('formAlertBox');
        const submitBtn = document.getElementById('submitBtn');
        const submitBtnText = document.getElementById('submitBtnText');

        const fieldIds = ['name', 'email', 'phone', 'subject', 'message'];

        function clearErrors() {
            fieldIds.forEach(function(field) {
                const errorEl = document.getElementById('error-' + field);
                const inputEl = document.getElementById(field);
                if (errorEl) errorEl.textContent = '';
                if (inputEl) inputEl.classList.remove('is-invalid');
            });
            alertBox.innerHTML = '';
        }

        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                clearErrors();
                submitBtn.disabled = true;
                submitBtnText.textContent = 'Sending...';

                const formData = new FormData(form);
                formData.append('contact_submit', '1');

                fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    submitBtn.disabled = false;
                    submitBtnText.textContent = 'Send Message';

                    if (data.success) {
                        alertBox.innerHTML =
                            '<div class="alert-success mt-6">' +
                            '<i class="fa-solid fa-circle-check text-lg"></i>' +
                            '<span>Thank you! Your message has been sent. We\'ll get back to you soon.</span>' +
                            '</div>';
                        form.reset();
                        alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    } else {
                        const errors = data.errors || {};

                        if (errors.general) {
                            alertBox.innerHTML =
                                '<div class="alert-danger mt-6">' +
                                '<i class="fa-solid fa-circle-exclamation text-lg"></i>' +
                                '<span>' + errors.general + '</span>' +
                                '</div>';
                        }

                        fieldIds.forEach(function(field) {
                            if (errors[field]) {
                                const errorEl = document.getElementById('error-' + field);
                                const inputEl = document.getElementById(field);
                                if (errorEl) errorEl.textContent = errors[field];
                                if (inputEl) inputEl.classList.add('is-invalid');
                            }
                        });
                    }
                })
                .catch(function() {
                    submitBtn.disabled = false;
                    submitBtnText.textContent = 'Send Message';
                    alertBox.innerHTML =
                        '<div class="alert-danger mt-6">' +
                        '<i class="fa-solid fa-circle-exclamation text-lg"></i>' +
                        '<span>Something went wrong. Please check your connection and try again.</span>' +
                        '</div>';
                });
            });
        }
    });
</script>