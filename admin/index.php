<?php

require_once "../bootstrap.php";

$db = new DBconnection();

$event = new Event($db);
$category = new Category($db);
$ticket = new Ticket($db);

$total_event = count($event->find_all());
$total_category = count($category->find_all());
$total_ticket = count($ticket->find_all());

require_once "templates/header.php";
require_once "templates/navbar.php";
require_once "templates/sidebar.php";
?>

<main class="app-main">

    <div class="app-content-header">

        <div class="container-fluid">

            <h3 class="mb-0">
                Dashboard
            </h3>

        </div>

    </div>


    <div class="app-content">

        <div class="container-fluid">

            <div class="row">

                <!-- Total Event -->
                <div class="col-lg-3 col-6">

                    <div class="small-box text-bg-primary">

                        <div class="inner">

                            <h3>
                                <?= $total_event ?>
                            </h3>

                            <p>
                                Total Event
                            </p>

                        </div>

                        <a
                            href="events/index.php"
                            class="small-box-footer"
                        >
                            Kelola Event
                            <i class="bi bi-arrow-right"></i>
                        </a>

                    </div>

                </div>


                <!-- Total Category -->
                <div class="col-lg-3 col-6">

                    <div class="small-box text-bg-success">

                        <div class="inner">

                            <h3>
                                <?= $total_category ?>
                            </h3>

                            <p>
                                Total Category
                            </p>

                        </div>

                        <a
                            href="categories/index.php"
                            class="small-box-footer"
                        >
                            Kelola Category
                            <i class="bi bi-arrow-right"></i>
                        </a>

                    </div>

                </div>


                <!-- Total Ticket -->
                <div class="col-lg-3 col-6">

                    <div class="small-box text-bg-warning">

                        <div class="inner">

                            <h3>
                                <?= $total_ticket ?>
                            </h3>

                            <p>
                                Total Ticket
                            </p>

                        </div>

                        <a
                            href="tickets/index.php"
                            class="small-box-footer"
                        >
                            Kelola Ticket
                            <i class="bi bi-arrow-right"></i>
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</main>


<?php
require_once "templates/footer.php";
?>