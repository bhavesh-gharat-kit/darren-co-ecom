<?php
session_start();

// Fix the paths - go up two levels to reach root
require_once '../../config/constant.php';
require_once '../../config/db.php';

$error = "";

if (isset($_SESSION['admin_id'])) {
    header("Location: " . ADMIN_URL . "dashboard.php");
    exit;
}

if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Please fill all fields.";
    } else {
        $admin = $database->get("admins", "*", [
            "email" => $email
        ]);

        if (!$admin) {
            $error = "Invalid Email.";
        } elseif ($admin['status'] != "Active") {
            $error = "Your account is inactive.";
        } elseif (!password_verify($password, $admin['password'])) {
            $error = "Invalid Password.";
        } else {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_email'] = $admin['email'];

            header("Location: " . ADMIN_URL . "dashboard.php");
            exit;
        }
    }
}

include '../includes/header.php';
?>

<div class="min-h-screen flex items-center justify-center bg-slate-100">

    <div class="w-full max-w-md bg-white shadow-xl rounded-xl p-8">

        <h2 class="text-3xl font-bold text-center mb-2">
            Admin Login
        </h2>

        <p class="text-center text-gray-500 mb-6">
            Login to your admin account
        </p>

        <?php if (!empty($error)) : ?>

            <div class="mb-4 rounded-lg bg-red-100 border border-red-300 text-red-700 px-4 py-3">
                <?php echo $error; ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <!-- Email -->
            <div class="mb-5">

                <label class="block text-sm font-medium mb-2">
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                    placeholder="Enter Email"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>

            </div>

            <!-- Password -->
            <div class="mb-6">

                <label class="block text-sm font-medium mb-2">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    placeholder="Enter Password"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>

            </div>

            <!-- Login Button -->
            <button
                type="submit"
                name="login"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold transition">

                Login

            </button>

        </form>

    </div>

</div>

<?php include '../includes/footer.php'; ?>