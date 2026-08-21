<div class="header-offcanvas offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
    <!-- close icon -->
    <a href="#!" class="btn offcanvas-close text-reset" data-bs-dismiss="offcanvas">
        <i class="las la-times"></i>
    </a>
    <div class="offcanvas-body p-0">
        <ul class="nav nav-pills" id="pills-tab" role="tablist">
            <li class="nav-item " role="presentation">
                <button class="nav-link active text-uppercase" id="pills-menu-tab" data-bs-toggle="pill" data-bs-target="#pills-menu" type="button" role="tab" aria-controls="pills-menu" aria-selected="true">Menu</button>
            </li>
        </ul>
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-menu" role="tabpanel" aria-labelledby="pills-menu-tab" tabindex="0">
                <div class="accordion" id="accordionPanelsStayOpenExample">
                    <?php
                        $c1 = mysqli_query($con, 'SELECT * FROM producttype');
                        while ($c = mysqli_fetch_row($c1)) 
                        {
                    ?>

                    <a href="shop?pt_id=<?php echo $c[0]; ?>" class="pill-item col-6 p-0" role="presentation">
                        <button class="nav-link" type="button"><?php echo htmlspecialchars($c[1], ENT_QUOTES, 'UTF-8'); ?></button>
                    </a>
                    <?php
                        }
                        $f1 = mysqli_query($con, 'SELECT * FROM collection');
                        if ($f = mysqli_fetch_row($f1)) 
                        {
                    ?>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="panelsStayOpen-headingFive">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFive" aria-expanded="false" aria-controls="panelsStayOpen-collapseFive">
                               Collection
                            </button>
                        </h2>
                        <div id="panelsStayOpen-collapseFive" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingFive">
                            <ul class="accordion-nav-list list-unstyled mb-0">
                                <?php
                                    $c1 = mysqli_query($con, 'SELECT * FROM collection');
                                    while ($c = mysqli_fetch_row($c1)) 
                                    {
                                ?>
                                <li><a href="shop?collect_id=<?php echo $c[0]; ?>"><?php echo htmlspecialchars($c[1], ENT_QUOTES, 'UTF-8'); ?></a></li>
                                <?php
                                    }
                                ?>
                            </ul>
                        </div>
                    </div>
                    <?php
                        }
                    ?>
                    <a href="stores_events" class="pill-item col-6 p-0" role="presentation">
                        <button class="nav-link" type="button">Store</button>
                    </a>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="panelsStayOpen-headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFour" aria-expanded="false" aria-controls="panelsStayOpen-collapseFour">
                                Get In Touch
                            </button>
                        </h2>
                        <div id="panelsStayOpen-collapseFour" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingFour">
                            <ul class="accordion-nav-list list-unstyled mb-0">
                                <li><a href="term-of-use">Terms & Condition</a></li>
                                <li><a href="return_refund">Return & Refund Policy</a></li>
                                <li><a href="cancellation_policy">Cancellation Policy</a></li>
                                <li><a href="shipping_policy">Shipping Policy</a></li>
                                <li><a href="privacy_policy">Privacy Policy</a></li>
                                <li><a href="disclaimer">Disclaimer</a></li>
                                <li><a href="faq">FAQ's</a></li>
                                <li><a href="contact">Contact Us</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>