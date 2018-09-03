<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 6/15/18
 * Time: 3:02 AM
 *
 * @author haronarama
 */

include($_SERVER['DOCUMENT_ROOT'] . "/property-manager/include/autoload.php");

// component constants
$PAGE_ID = 4;
$USER = "Richard"; // user is set from initial configuration

$page = new Web_Page($PAGE_ID, $USER);
$data = new Data_Table("$PAGE_ID-tenants", "tenants-table", $page);
$page->setTitle("Tenants");
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
        <h1 class="h2">Tenants</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group mr-2">
                <button class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#addTenantModal">Add Tenant</button>
                <button class="btn btn-sm btn-outline-secondary">Import</button>
                <button class="btn btn-sm btn-outline-secondary">Export</button>
            </div>
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle">
                <span data-feather="calendar"></span>
                This week
            </button>
        </div>
    </div>

    <!-- Add Tenant Modal -->
    <form class="form-group" action="" method="post">
        <div class="modal fade" id="addTenantModal" tabindex="-1" role="dialog" aria-labelledby="addTenantLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addTenantLabel">Add a New Tenant</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h4 class="border-gray border-bottom">Personal Info</h4>

                        <div class="form-label-group">
                            <input type="text" name="firstName" id="firstName" class="form-control" placeholder="First Name">
                            <label for="firstName">First Name</label>
                        </div>

                        <div class="form-label-group">
                            <input type="text" name="middleName" id="middleName" class="form-control" placeholder="Middle Name">
                            <label for="middleName">Middle Name</label>
                        </div>

                        <div class="form-label-group">
                            <input type="text" name="lastName" id="lastName" class="form-control" placeholder="Last Name">
                            <label for="lastName">Last Name</label>
                        </div>

                        <div class="form-label-group">
                            <input type="text" name="phone" id="phone" class="form-control" placeholder="Phone">
                            <label for="phone">Phone</label>
                        </div>

                        <label for="honorific">Honorific</label>
                        <div class="form-label-group">
                            <select type="text" name="honorific" id="honorific" class="form-control">
                                <option value="">None</option>
                                <option value="Mr.">Mr.</option>
                                <option value="Mrs.">Mrs.</option>
                                <option value="Ms.">Ms.</option>
                            </select>
                        </div>

                        <label for="gender">Gender</label>
                        <div class="form-label-group">
                            <select type="text" name="gender" id="gender" class="form-control">
                                <option value="">None</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>

                        <h4 class="border-bottom border-gray">Financial Info</h4>

                        <div class="form-label-group">
                            <input type="number" min="500" step=".01" name="income" id="income" class="form-control" placeholder="Income">
                            <label for="income">Income</label>
                        </div>

                        <div class="form-label-group">
                            <input type="number" step=".01" name="rent" id="rent" class="form-control" placeholder="Rent">
                            <label for="rent">Rent</label>
                        </div>

                        <h4 class="border-bottom border-gray">Property Info</h4>
                        <?php
                            $data->selectProperty("property", "property");
                        ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <input type="submit" class="btn btn-primary">
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel" data-pause="hover">
        <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
<!--            <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>-->
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <h3>My Tenants</h3>
                <?php
                $query = "SELECT t.id, t.first_name, t.middle_name, t.last_name, t.rent, u.name, p.address, t.payment_status, t.notes 
                          FROM `tenants` t 
                          INNER JOIN `units` u 
                          ON t.`units_id`=u.`id`
                          JOIN properties p
                          ON p.id = u.properties_id
                          AND t.`status`='Active'";

                $columns = array(
                    "id",
                    "first_name",
                    "middle_name",
                    "last_name",
                    "rent",
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
                    "Address",
                    "Payment Status",
                    "Notes",
                    "Action"
                );

                $data->createTableFromQuery($query, $columns, $column_names, true, $data->table_id);
                ?>
            </div>
            <div class="carousel-item">
                <h3>Rent Graph</h3>
<!--                <canvas class="my-4" id="myChart" width="900" height="380"></canvas>-->
                <img class="d-block w-100" src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22800%22%20height%3D%22400%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20800%20400%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_16404fff51b%20text%20%7B%20fill%3A%23333%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A40pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_16404fff51b%22%3E%3Crect%20width%3D%22800%22%20height%3D%22400%22%20fill%3D%22%23555%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%22277%22%20y%3D%22218.3%22%3EComing%20soon%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E" alt="Coming soon">
            </div>
<!--            <div class="carousel-item">-->
<!--                <img class="d-block w-100" src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22800%22%20height%3D%22400%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20800%20400%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_16404fff51b%20text%20%7B%20fill%3A%23333%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A40pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_16404fff51b%22%3E%3Crect%20width%3D%22800%22%20height%3D%22400%22%20fill%3D%22%23555%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%22277%22%20y%3D%22218.3%22%3EThird%20slide%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E" alt="Third slide">-->
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
//$data->editDataTableEntry();

$page->addScript("
    <script>
        $(document).ready(function() {
            $('#$PAGE_ID-tenants').DataTable();
        } );
    </script>
");
$page->printFooter();
?>