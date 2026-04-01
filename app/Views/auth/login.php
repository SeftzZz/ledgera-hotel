<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $title ?></title>
        <link rel="shortcut icon" type="image/x-icon" href="<?= base_url('assets/img/favicon/favicon.ico') ?>">
        <!-- Normalize -->
        <link rel="stylesheet" href="<?= base_url('assets/login/normalize-5.0.0.min.css') ?>">
        <!-- CSS -->
        <link rel="stylesheet" href="<?= base_url('assets/login/style.css') ?>">
    </head>

    <body>
        <div class="scroll-down">
            SCROLL DOWN
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                <path d="M16 3C8.832031 3 3 8.832031 3 16s5.832031 13 13 13 13-5.832031 13-13S23.167969 3 16 3zm0 2c6.085938 0 11 4.914063 11 11 0 6.085938-4.914062 11-11 11-6.085937 0-11-4.914062-11-11C5 9.914063 9.914063 5 16 5zm-1 4v10.28125l-4-4-1.40625 1.4375L16 23.125l6.40625-6.40625L21 15.28125l-4 4V9z"/> 
            </svg>
        </div>

        <div class="container"></div>

        <div class="modal">
            <div class="modal-container">
                <div class="modal-left">
                    <form action="<?= base_url('login') ?>" method="post">
                        <?= csrf_field() ?>
                        <h1 class="modal-title">Welcome</h1>
                        <p class="modal-desc">Please login</p>

                        <div class="input-block">
                            <label class="input-label">Email</label>
                            <input type="email" name="email" utofocus="autofocus" placeholder="Email" required>
                        </div>

                        <div class="input-block">
                            <label class="input-label">Password</label>
                            <input type="password" name="password" placeholder="Password" required>
                        </div>

                        <div class="modal-buttons">
                            <a href="#">Forgot your password?</a>
                            <button class="input-button">Login</button>
                        </div>
                    </form>

                    <!-- <p class="sign-up">
                        Don't have an account? <a href="#">Sign up</a>
                    </p> -->
                </div>

                <div class="modal-right">
                    <img src="<?= base_url('assets/login/photo-1512486130939-2c4f79935e4f.png?auto=format&fit=crop&w=1000&q=80') ?>">
                </div>

                <button class="icon-button close-button" aria-label="Close modal">
                    ✕
                </button>
            </div>
            <button class="modal-button">Click here to login</button>
        </div>

        <!-- JS -->
        <script>
            const body = document.body;
            const modal = document.querySelector(".modal");
            const modalButton = document.querySelector(".modal-button");
            const closeButton = document.querySelector(".close-button");
            const scrollDown = document.querySelector(".scroll-down");

            let isOpened = false;

            const openModal = () => {
              modal.classList.add("is-open");
              body.style.overflow = "hidden";
            };

            const closeModal = () => {
              modal.classList.remove("is-open");
              body.style.overflow = "auto";
            };

            window.addEventListener("scroll", () => {
              if (window.scrollY > window.innerHeight / 3 && !isOpened) {
                isOpened = true;
                scrollDown.style.display = "none";
                openModal();
              }
            });

            modalButton.addEventListener("click", openModal);
            closeButton.addEventListener("click", closeModal);

            document.addEventListener("keydown", e => {
              if (e.key === "Escape") closeModal();
            });
        </script>
    </body>
</html>