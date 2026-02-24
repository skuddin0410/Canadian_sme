<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>pricing</title>
    <!-- bootstrap css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <!-- icon -->
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- font family -->
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap"
        rel="stylesheet">
    <!-- Swiper CSS CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <!-- css -->
    <link rel="stylesheet" href="./frontend/css/style_new.css">
</head>

<body>

    <!-- header -->
    @include('partials_new.header')
    <!-- header end -->

    <!-- pricing start -->
    <section class="pricing section">
        <div class="container">
            <div class="global-heading text-center">
                <h2>
                    Lorem ipsum dolor, sit amet consectetur adipisicing elit.
                </h2>
                <p>
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus eum quis nihil architecto cumque
                    dignissimos.
                </p>
            </div>

            <div class="d-flex justify-content-center mt-3 mt-lg-4 mt-xl-5">
                <div class="form-switch-outer">

                    <span>Monthly</span>

                    <div class="form-check form-switch m-0">
                        <input class="form-check-input" type="checkbox" id="billingSwitch">
                    </div>

                    <span class="yearly">Yearly</span>

                    <!-- Discount Badge -->
                    <div class="discount-badge-outer d-flex align-items-center gap-2">
                        <img src="./images/green-left-arrow.png" alt="">
                        <span class="badge discount-badge">
                            Save up to 10%
                        </span>
                    </div>

                </div>
            </div>

            <div class="row pricing-card-row justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <div class="pricing-card">
                        <div class="pricing-card-top">
                            <div class="d-flex align-items-center gap-2 gap-lg-3">
                                <div class="pricing-card-img-wrapper">
                                    <img src="./images/free-plan.png" alt="">
                                </div>
                                <h3>Free plan</h3>
                            </div>
                            <p>Lorem ipsum dolor sit amet consectetur. Viverra sit.</p>
                            <h4>
                                $00
                                <span class="per text-grey">/month</span>
                            </h4>
                            <h5>
                                Includes:
                            </h5>
                            <ul class="ul">
                                <li><i class="fa-solid fa-circle-check"></i>Lorem ipsum dolor sit amet.</li>
                                <li><i class="fa-solid fa-circle-check"></i>Lorem ipsum dolor sit amet.</li>
                                <li><i class="fa-solid fa-circle-check"></i>Lorem ipsum dolor sit amet.</li>
                                <li><i class="fa-solid fa-circle-check"></i>Lorem ipsum dolor sit amet.</li>
                                <li><i class="fa-solid fa-circle-check"></i>Lorem ipsum dolor sit amet.</li>
                            </ul>
                        </div>
                        <div class="pricing-card-bottom">
                            <button class="btn pricing-card-btn w-100">Free Plan</button>
                        </div>

                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="pricing-card">
                        <div class="pricing-card-top">
                            <div class="d-flex align-items-center gap-2 gap-lg-3">
                                <div class="pricing-card-img-wrapper">
                                    <img src="./images/standard-plan.png" alt="">
                                </div>
                                <h3>Standard plan</h3>
                            </div>
                            <p>Lorem ipsum dolor sit amet consectetur. Dignissim purus odio sit odio integer consequat
                                ac.</p>
                            <h4>
                                $XX
                                <span class="per text-grey">/month</span>
                            </h4>
                            <h5>
                                Includes:
                            </h5>
                            <ul class="ul">
                                <li><i class="fa-solid fa-circle-check"></i>Lorem ipsum dolor sit amet.</li>
                                <li><i class="fa-solid fa-circle-check"></i>Lorem ipsum dolor sit amet.</li>
                                <li><i class="fa-solid fa-circle-check"></i>Lorem ipsum dolor sit amet.</li>
                                <li><i class="fa-solid fa-circle-check"></i>Lorem ipsum dolor sit amet.</li>
                                <li><i class="fa-solid fa-circle-check"></i>Lorem ipsum dolor sit amet.</li>
                                <li><i class="fa-solid fa-circle-check"></i>Lorem ipsum dolor sit amet.</li>
                                <li><i class="fa-solid fa-circle-check"></i>Lorem ipsum dolor sit amet.</li>
                            </ul>
                        </div>
                        <div class="pricing-card-bottom">
                            <button class="btn pricing-card-btn w-100">Free Plan</button>
                        </div>

                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="pricing-card most-popular position-relative">
                        <div class="most-popular-badge">Most Popular</div>
                        <div class="pricing-card-top">
                            <div class="d-flex align-items-center gap-2 gap-lg-3">
                                <div class="pricing-card-img-wrapper">
                                    <img src="./images/standard-plan.png" alt="">
                                </div>
                                <h3>Elite plan</h3>
                            </div>
                            <p>Lorem ipsum dolor sit amet consectetur. Ullamcorper quis mollis venenatis id donec</p>
                            <h4>
                                $XX
                                <span class="per text-grey">/month</span>
                            </h4>
                            <h5>
                                Includes:
                            </h5>
                            <ul class="ul">
                                <li><i class="fa-solid fa-circle-check"></i>Lorem ipsum dolor sit amet.</li>
                                <li><i class="fa-solid fa-circle-check"></i>Lorem ipsum dolor sit amet.</li>
                                <li><i class="fa-solid fa-circle-check"></i>Lorem ipsum dolor sit amet.</li>
                                <li><i class="fa-solid fa-circle-check"></i>Lorem ipsum dolor sit amet.</li>
                                <li><i class="fa-solid fa-circle-check"></i>Lorem ipsum dolor sit amet.</li>
                                <li><i class="fa-solid fa-circle-check"></i>Lorem ipsum dolor sit amet.</li>
                                <li><i class="fa-solid fa-circle-check"></i>Lorem ipsum dolor sit amet.</li>
                                <li><i class="fa-solid fa-circle-check"></i>Lorem ipsum dolor sit amet.</li>
                                <li><i class="fa-solid fa-circle-check"></i>Lorem ipsum dolor sit amet.</li>
                            </ul>
                        </div>
                        <div class="pricing-card-bottom">
                            <button class="btn pricing-card-btn w-100">Free Plan</button>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </section>
    <!-- pricing end -->

    <!-- pricing-table-section start -->
    <section class="pricing-table-section section">
        <div class="container">
            <div class="global-heading text-center">
                <h2>
                    Lorem ipsum dolor, sit amet consectetur adipisicing elit.
                </h2>
                <p>
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus eum quis nihil architecto cumque
                    dignissimos.
                </p>
            </div>
            <div class="pricing-table-wrapper">
                <table class="table bg-white mb-0">
                    <thead>
                        <tr>
                            <th class="Features">Features</th>
                            <th class="text-center">Free</th>
                            <th class="text-center">Standard</th>
                            <th class="text-center text-theme">Elite</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td valign="middle">Lorem ipsum dolor sit amet.</td>
                            <td class="align-middle text-center"><img class="check-cross" src="./images/red-cross.png"
                                    alt=""></td>
                            <td class="align-middle text-center"><img class="check-cross" src="./images/green-check.png"
                                    alt=""></td>
                            <td class="align-middle text-center"><img class="check-cross" src="./images/red-cross.png"
                                    alt=""></td>
                        </tr>
                        <tr>
                            <td valign="middle">Lorem ipsum dolor sit amet.</td>
                            <td class="align-middle text-center"><img class="check-cross" src="./images/green-check.png"
                                    alt=""></td>
                            <td class="align-middle text-center"><img class="check-cross" src="./images/green-check.png"
                                    alt=""></td>
                            <td class="align-middle text-center"><img class="check-cross" src="./images/red-cross.png"
                                    alt=""></td>
                        </tr>
                        <tr>
                            <td valign="middle">Lorem ipsum dolor sit amet.</td>
                            <td class="align-middle text-center"><img class="check-cross" src="./images/green-check.png"
                                    alt=""></td>
                            <td class="align-middle text-center"><img class="check-cross" src="./images/green-check.png"
                                    alt=""></td>
                            <td class="align-middle text-center"><img class="check-cross" src="./images/green-check.png"
                                    alt=""></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <!-- pricing-table-section end -->

    <!-- faq start -->
    <section class="faq section">
        <div class="container">
            <div class="global-heading text-center">
                <h2>
                    Lorem ipsum dolor, sit amet consectetur adipisicing elit.
                </h2>
                <p>
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus eum quis nihil architecto cumque
                    dignissimos.
                </p>
            </div>
            <div class="accordion" id="accordionExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Lorem ipsum dolor sit amet consectetur. Commodo platea.
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            Lorem ipsum dolor sit amet consectetur. A sit nulla lectus viverra libero elementum
                            tristique rhoncus. Nullam lacus accumsan netus pharetra ut. Placerat consectetur varius
                            venenatis nulla quis. Convallis vulputate eget in sed vel porta. Lectus eget facilisis
                            mauris neque.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Accordion Item #2
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <strong>This is the second item's accordion body.</strong> It is hidden by default, until
                            the collapse plugin adds the appropriate classes that we use to style each element. These
                            classes control the overall appearance, as well as the showing and hiding via CSS
                            transitions. You can modify any of this with custom CSS or overriding our default variables.
                            It's also worth noting that just about any HTML can go within the
                            <code>.accordion-body</code>, though the transition does limit overflow.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            Accordion Item #3
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <strong>This is the third item's accordion body.</strong> It is hidden by default, until the
                            collapse plugin adds the appropriate classes that we use to style each element. These
                            classes control the overall appearance, as well as the showing and hiding via CSS
                            transitions. You can modify any of this with custom CSS or overriding our default variables.
                            It's also worth noting that just about any HTML can go within the
                            <code>.accordion-body</code>, though the transition does limit overflow.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- faq end -->

    <!-- footer -->
    @include('partials_new.footer')
    <!-- footer end -->







    <!-- js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <script src="./frontend/js/script_new.js"></script>
    <!-- Swiper JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

</body>

</html>