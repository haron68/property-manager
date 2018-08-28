<?php
include($_SERVER['DOCUMENT_ROOT'] . "/property-manager/include/autoload.php");

// component constants
$PAGE_ID = 0;
$USER = "Richard"; // user is set from initial configuration

$page = new Web_Page($PAGE_ID, $USER);
$data = new Data_Table("$PAGE_ID", "tenants-table", $page);
$chart = new Data_Chart("$PAGE_ID", "rent-chart", $page);
$page->setTitle("Home");
$page->addStylesheet("
<style>
/**
 * Carousel
 */
.carousel-control-prev,
.carousel-control-next {
    width: 2%;
}
</style>
");
$page->printHeader();

if (isset($_GET['id'])) {
    $data->deleteTenant($_GET['id'], true, true);
}
?>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group mr-2">
                <button class="btn btn-sm btn-outline-secondary">Share</button>
                <button class="btn btn-sm btn-outline-secondary">Export</button>
            </div>
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle">
                <span data-feather="calendar"></span>
                This week
            </button>
        </div>
    </div>

    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel" data-pause="hover">
        <ol class="carousel-indicators">
<!--            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li> <!-- month finance overview report -->
            <li data-target="#carouselExampleIndicators" data-slide-to="1" class="active"></li>    <!-- unit report -->
<!--            <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>    <!-- rent report -->
            <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>    <!-- tenant overview who's late is highlighted red -->
<!--            <li data-target="#carouselExampleIndicators" data-slide-to="4"></li>    <!-- Income/Expenditure last 10 years -->
        </ol>
        <div class="carousel-inner">
<!--            <div class="carousel-item active">-->
<!--                <h3>Monthly Finance Overview</h3>-->
<!--                <img class="d-block w-100" src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22800%22%20height%3D%22400%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20800%20400%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_16404fff51b%20text%20%7B%20fill%3A%23333%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A40pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_16404fff51b%22%3E%3Crect%20width%3D%22800%22%20height%3D%22400%22%20fill%3D%22%23555%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%22277%22%20y%3D%22218.3%22%3EThird%20slide%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E" alt="slide">-->
<!--            </div>-->
            <div class="carousel-item active">
                <h3>Properties and Units</h3>
                <?php
                $query = "SELECT *, COUNT(t.id) FROM units AS u JOIN tenants AS t ON t.units_id = u.id";
                $columns = array(
                    "id",
                    "name",
                    "address",
                    "rental_status",
                    "rent_price",
                    "COUNT(t.id)",
                    "notes"
                );

                $column_names = array(
                    "ID",
                    "Name",
                    "Address",
                    "Rental Status",
                    "Rent Price",
                    "Renters",
                    "Notes",
                    "Actions"
                );

                $data->createTableFromQuery($query, $columns, $column_names, true, "$data->table_id-units");
                ?>
            </div>
<!--            <div class="carousel-item">-->
<!--                <h3>Rent Graph</h3>-->
<!--                <canvas class="my-4" id="myChart" width="900" height="380"></canvas>-->
<!--            </div>-->
            <div class="carousel-item">
                <h3>My Tenants</h3>
                <?php
                $query = "SELECT t.id, t.first_name, t.middle_name, t.last_name, t.rent, u.name, u.address, t.payment_status, t.notes FROM `tenants` t INNER JOIN `units` u ON t.`units_id`=u.`id` AND t.`status`='Active'";
                $columns = array(
                    "id",
                    "first_name",
                    "middle_name",
                    "last_name",
                    "rent",
                    "name",
                    "address",
                    "payment_status",
                    "notes"
                );

                $column_names = array(
                    "ID",
                    "First Name",
                    "Middle Name",
                    "Last Name",
                    "Rent",
                    "Unit Name",
                    "Address",
                    "Payment Status",
                    "Notes",
                    "Actions"
                );

                $data->createTableFromQuery($query, $columns, $column_names, true, "$data->table_id-tenants");
                ?>
            </div>
<!--            <div class="carousel-item">-->
<!--                <h3>Income/Expenditure Last Decade</h3>-->
<!--                <img class="d-block w-100" src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22800%22%20height%3D%22400%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20800%20400%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_16404fff51b%20text%20%7B%20fill%3A%23333%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A40pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_16404fff51b%22%3E%3Crect%20width%3D%22800%22%20height%3D%22400%22%20fill%3D%22%23555%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%22277%22%20y%3D%22218.3%22%3EThird%20slide%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E" alt="slide">-->
<!--            </div>-->
        </div>
        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
<?php
//$chart->createChart(array(15339, 21345, 18483, 24003, 23489, 24092, 12034), array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'), "myChart");
$page->addScript("
    <script>
        $(document).ready(function() {
            $('#$PAGE_ID-tenants').DataTable();
        } );
        $(document).ready(function() {
            $('#$PAGE_ID-units').DataTable();
        } );
    </script>
");
$page->printFooter();
?>