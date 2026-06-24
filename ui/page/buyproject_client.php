<?php 
include '../../header_dashboard.php';?>


<main class="mainProject">

<section class="bg_personal min-vh-100 py-5">
    <div class="container">

        <h2 class="display-3 fw-bold text-warning text-center mb-3">
            Software & Resources
        </h2>

        <p class="text-white text-center mb-5">
            Discover all available products and resources.
        </p>

        <div class="row g-4">
        
            <!-- CARD 1 -->
            <div class="col-lg-4 col-md-6">
                <div class="card software-card bg-dark text-white h-100">
                    <img src="../../images/web.png" class="card-img-top" alt="Landing Web">

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-warning">
                            Landing Web
                        </h5>

                        <p class="card-text text-light">
                            Responsive Bootstrap landing page template.
                            Ready to customise with your brand, hero sections and contact form.
                        </p>

                        <div class="mt-auto d-flex justify-content-between align-items-center">
                            <span class="software-price">€19</span>
                            <a href="#" class="btn btn-outline-warning">
                                Buy Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD 2 -->
            <div class="col-lg-4 col-md-6">
                <div class="card software-card bg-dark text-white h-100">
                    <img src="../../images/software.png" class="card-img-top" alt="Software">

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-warning">
                            Software
                        </h5>

                        <p class="card-text text-light">
                            Complete software solution ready to deploy
                            and customize according to your business needs.
                        </p>

                        <div class="mt-auto d-flex justify-content-between align-items-center">
                            <span class="software-price">€49</span>
                            <a href="#" class="btn btn-outline-warning">
                                Buy Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD 3 -->
            <div class="col-lg-4 col-md-6">
                <div class="card software-card bg-dark text-white h-100">
                    <img src="../../images/frontend.png" class="card-img-top" alt="Frontend">

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-warning">
                            Frontend
                        </h5>

                        <p class="card-text text-light">
                            Modern frontend template built with Bootstrap
                            and ready for React integration.
                        </p>

                        <div class="mt-auto d-flex justify-content-between align-items-center">
                            <span class="software-price">€29</span>
                            <a href="#" class="btn btn-outline-warning">
                                Buy Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD 4 -->
            <div class="col-lg-4 col-md-6">
                <div class="card software-card bg-dark text-white h-100">
                    <img src="../../images/api.png" class="card-img-top" alt="API">

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-warning">
                            API
                        </h5>

                        <p class="card-text text-light">
                            REST API starter project with authentication,
                            CRUD operations and documentation.
                        </p>

                        <div class="mt-auto d-flex justify-content-between align-items-center">
                            <span class="software-price">€39</span>
                            <a href="#" class="btn btn-outline-warning">
                                Buy Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD 5 -->
            <div class="col-lg-4 col-md-6">
                <div class="card software-card bg-dark text-white h-100">
                    <img src="../../images/backend.png" class="card-img-top" alt="Backend">

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-warning">
                            Backend
                        </h5>

                        <p class="card-text text-light">
                            PHP and MySQL backend ready for production
                            with authentication and admin area.
                        </p>

                        <div class="mt-auto d-flex justify-content-between align-items-center">
                            <span class="software-price">€59</span>
                            <a href="#" class="btn btn-outline-warning">
                                Buy Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>

</main>

<?php include __DIR__ . '/../components/footer.php' ?>