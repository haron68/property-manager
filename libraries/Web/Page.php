<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 6/15/18
 * Time: 12:25 AM
 *
 * @author haronarama
 */

class Web_Page
{
    /**
     * String value to keep track and validate product version
     * @var string
     */
    public $product_version = "0.0.1";

    /**
     * Boolean to determine whether or not to print page sidebar/menu
     * @var boolean
     */
    public $print_sidebar;

    /**
     * Boolean to determine whether or not to print page sidebar/menu
     * @var boolean
     */
    public $print_top_nav;

    /**
     * String value for page title default is "Dad's Property Manager"
     * @var string
     */
    public $page_title;

    /**
     * Any style sheet that should be loaded in the page header
     * @var string
     */
    public $stylesheet = "";

    /**
     * Any javaScript that should be appended to the footer to load faster
     * @var string
     */
    public $script = "";

    /**
     * The database handler
     * @var PDO
     */
    public $db;

    /**
     * Value to identify user
     * @var string
     */
    public $user;

    /**
     * value to determine project root path
     * @var string
     */
    public $root_path = "http://localhost/property-manager";

    /**
     * User class: 0 - Admin, 1 - User
     * @var integer
     */
    public $user_class = 1;

    /**
     * Unique page id that will allow admin to set settings for potential additional users
     * @var integer 0 - Home, 1 - Products, 2 - Reports, 3 - Settings, 4 - Tenants
     */
    public $page_id;

    /**
     * Whether or not user is able to edit content
     * @var boolean
     */
    public $editor = false;

    /**
     * Constructor for the page. Sets up most of the properties of this object.
     *
     * @param $page_id
     * @param $page_title
     * @param $user
     * @param $print_sidebar
     */
    function __construct($page_id, $user, $page_title = "Property Manager", $print_sidebar = true)
    {
        $this->page_title = $page_title;
        $this->print_sidebar = $print_sidebar;

        // Connect to database
        $this->db = $this->connect();

        //Set up $this->user
        $this->user = $user;

        $this->page_id = $page_id;

        $select_query = "SELECT COUNT(payment_status) FROM tenants WHERE payment_status != 'Paid'";
        $result = $this->db->query($select_query);
        $row    = $result->fetch(PDO::FETCH_ASSOC);
        $count = $row['COUNT(payment_status)'];
        if ($count > 0) {
            mail("arama006@umn.edu", "Late renter's notice!", wordwrap("There are currently $count tenants with late rents! Go back to the Property Manager to view this issue!", 70));
        }
    }

    /**
     * Connect to the database.
     * @see PDO
     */
    function connect()
    {
        //Set up PDO connection
        try {
            $db = new PDO("mysql:host=localhost;dbname=local", "haron", "Ha7780703");
            $db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
            return $db;
        } catch (PDOException $e) {
            echo "Error: Unable to load this page. Please contact arama006@umn.edu for assistance.";
            echo "<br/>Error: " . $e;
        }
    }

    /**
     * Set the title of the page.
     * Must be called before printHeader.
     * @param $page_title string The title of the page.
     */
    function setTitle($page_title)
    {
        if (trim($page_title) != "") {
            $this->page_title .= " - " . $page_title;
        }
    }

    /**
     * Set any stylesheets that need to be loaded in the header.
     * Must be called before printHeader.
     * @param $add_stylesheet string The stylesheet to load.
     */
    function addStylesheet($add_stylesheet)
    {
        if (trim($add_stylesheet) != "") {
            $this->stylesheet = $add_stylesheet;
        }
    }

    /**
     * Set any stylesheets that need to be loaded in the header.
     * Must be called before printHeader.
     * @param $add_script string The stylesheet to load.
     */
    function addScript($add_script)
    {
        if (trim($add_script) != "") {
            $this->script .= $add_script;
        }
    }

    /**
     * Print page header
     * @param $print_top_nav
     */
    function printHeader($print_top_nav = true)
    {
        ?>
        <!doctype html>
        <html lang="en">
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                <meta name="description" content="">
                <meta name="author" content="">
                <link rel="icon" href="<?php echo $this->root_path; ?>/favicon.ico">

                <title><?php echo $this->page_title; ?></title>

                <!-- Bootstrap core CSS -->
                <link rel="stylesheet" href="<?php echo $this->root_path; ?>/dist/css/bootstrap.min.css" />

                <!-- Data tables CSS -->
                <link rel="stylesheet" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css" />

                <!-- Loader CSS -->
                <link rel="stylesheet" href="<?php echo $this->root_path; ?>/assets/css/loader.css" />
<!--                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"-->
<!--                      integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"-->
<!--                      crossorigin="anonymous">-->

                <!-- Custom styles for this template -->
                <?php echo $this->stylesheet; ?>
                <link href="<?php echo $this->root_path; ?>/assets/css/dashboard.css" rel="stylesheet">
            </head>

            <body onload="pageLoader()">
                <div id="loader"></div>
                <?php
                if ($print_top_nav) {
                    ?>
                    <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0">
                        <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="#">Properties Manager</a>
                        <input class="form-control form-control-dark w-100" type="text" placeholder="Search"
                               aria-label="Search">
                        <!--                    <ul class="navbar-nav px-3">-->
                        <!--                        <li class="nav-item text-nowrap">-->
                        <!--                            <a class="nav-link" href="#">Sign out</a>-->
                        <!--                        </li>-->
                        <!--                    </ul>-->
                    </nav>
                    <?php
                }
                ?>
                <div class="container-fluid animate-bottom">
                <div class="row">
                <?php $this->printSidebar($this->page_id, $this->print_sidebar); ?>
                <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4 animate-bottom">
                <?php
    }

    /**
     * Set permissions for the page. Will print Access Denied page and kill the page if needed.
     * @param $restrict_id integer The component to which access should be restricted.
     * @param $restrict_class integer The user class to which access should be restricted.
     * @param $restrict_user string Whether or not the page should be restricted to a certain user.
     */
    function setPermissions($restrict_id, $restrict_class, $restrict_user)
    {
        return;
    }


    /**
     * Print page sidebar
     * @param $page_id
     * @param $print_sidebar
     */
    function printSidebar($page_id, $print_sidebar = true)
    {

        if ($print_sidebar) {
            $active_home = "";
            $active_products = "";
            $active_reports = "";
            $active_settings = "";
            $active_tenants = "";
            $active_files = "";

            // Handle page navigation presentation logic
            switch ($page_id) {
                case 0:
                    $active_home = "active";
                    break;
                case 1:
                    $active_products = "active";
                    break;
                case 2:
                    $active_reports = "active";
                    break;
                case 3:
                    $active_settings = "active";
                    break;
                case 4:
                    $active_tenants = "active";
                    break;
                case 5:
                    $active_files = "active";
                default:
                    // none active
                    break;
            }
            ?>
            <nav class="col-md-2 d-none d-md-block bg-light sidebar">
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo $active_home; ?>"
                               href="<?php echo $this->root_path; ?>/home/">
                                <span data-feather="home"></span>
                                Dashboard <span class="sr-only">(current)</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $active_tenants; ?>"
                               href="<?php echo $this->root_path; ?>/components/tenants/">
                                <span data-feather="users"></span>
                                Tenants
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $active_products; ?>"
                               href="<?php echo $this->root_path; ?>/components/products/">
                                <span data-feather="shopping-cart"></span>
                                Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $active_reports; ?>"
                               href="<?php echo $this->root_path; ?>/components/reports/">
                                <span data-feather="bar-chart-2"></span>
                                Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $active_files; ?>"
                               href="<?php echo $this->root_path; ?>/components/files/">
                                <span data-feather="file"></span>
                                File Manager
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $active_settings; ?>"
                               href="<?php echo $this->root_path; ?>/components/settings/">
                                <span data-feather="settings"></span>
                                Settings
                            </a>
                        </li>
                    </ul>

                    <!--                 print saved reports-->
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Saved reports</span>
                        <a class="d-flex align-items-center text-muted" href="#">
                            <span data-feather="plus-circle"></span>
                        </a>
                    </h6>
                    <ul class="nav flex-column mb-2">
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <span data-feather="file-text"></span>
                                Current month
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <span data-feather="file-text"></span>
                                Last quarter
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <span data-feather="file-text"></span>
                                Social engagement
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <span data-feather="file-text"></span>
                                Year-end sale
                            </a>
                        </li>
                    </ul>
                </div>
                <footer><p class="text-muted">&copy;<?php echo date("Y"); ?> Haron Arama, version: <?php echo $this->product_version; ?></p></footer>
            </nav>
            <?php
        }
    }

    /**
     * Print page footer at the end of the page
     */
    function printFooter()
    {
        ?>
                        </main>
                    </div>
                </div>

                <!-- Loader -->
                <script>
                    var timeOut;

                    function pageLoader() {
                        timeOut = setTimeout(showPage, 1500);
                    }

                    function showPage() {
                        document.getElementById("loader").style.display = "none";
                        document.getElementById("myDiv").style.display = "block";
                    }
                </script>

                <!-- Bootstrap core JavaScript
                ================================================== -->
<!--                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"-->
<!--                        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"-->
<!--                        crossorigin="anonymous"></script>-->

                <!-- Placed at the end of the document so the pages load faster -->
                <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
                        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
                        crossorigin="anonymous"></script>
<!--                <script src="https://code.jquery.com/jquery-3.3.1.js"></script>-->
                <script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
                <script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
                <script>window.jQuery || document.write('<script src="<?php echo $this->root_path; ?>/assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
                <script src="<?php echo $this->root_path; ?>/assets/js/vendor/popper.min.js"></script>
                <script src="<?php echo $this->root_path; ?>/dist/js/bootstrap.min.js"></script>
<!--                <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"-->
<!--                        integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb"-->
<!--                        crossorigin="anonymous"></script>-->
<!--                <script src="/assets/js/vendor/holder.min.js"></script>-->

                <!-- Icons -->
                <script src="https://unpkg.com/feather-icons/dist/feather.min.js"></script>
                <script>
                    feather.replace()
                </script>

                <!-- Page scripts -->
            <?php echo $this->script; ?>
            </body>
        </html>

        <?php
    }
}