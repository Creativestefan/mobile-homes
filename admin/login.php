<?php
session_start();
require_once '../includes/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (isset($pdo)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['admin_logged_in'] = true;
                header("Location: index.php");
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    } else {
        $error = "Database not connected. Cannot log in.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | NextGen Homes</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0F172A;
            --secondary: #059669;
            --bg-gray: #F8FAFC;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .login-box {
            background: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo span {
            color: var(--secondary);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--primary);
        }

        input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            box-sizing: border-box;
            font-family: inherit;
        }

        input:focus {
            outline: none;
            border-color: var(--secondary);
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
        }

        button {
            width: 100%;
            padding: 0.75rem;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
            font-family: inherit;
            font-size: 1rem;
        }

        button:hover {
            background-color: #1E293B;
        }

        .error {
            background: #FEE2E2;
            color: #991B1B;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="login-box">
        <div class="logo">NextGen<span>Homes</span><br><span
                style="font-size: 1rem; font-weight: 500; color:#64748B;">Admin Portal</span></div>

        <?php if ($error): ?>
            <div class="error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Log In Securely</button>
        </form>


    </div>

</body>

</html>