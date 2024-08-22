<?php
include 'database.php';
// Add a new student
if (isset($_POST['add_student'])) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $dob = $_POST['dob'];
    $phoneno = $_POST['phoneno'];
    $address = $_POST['address'];

    $sql = "INSERT INTO students (firstname, lastname, dob, phoneno, address) VALUES ('$firstname', '$lastname', '$dob', '$phoneno', '$address')";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        echo "Record added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// update a student
if (isset($_POST['update_student'])) {
    $std_id = $_POST['std_id'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $dob = $_POST['dob'];
    $phoneno = $_POST['phoneno'];
    $address = $_POST['address'];

    $sql = "UPDATE students SET firstname='$firstname', lastname='$lastname', dob='$dob', phoneno='$phoneno', address='$address' WHERE std_id='$std_id'";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        echo "Record updated successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Delete a student
if (isset($_GET['delete_id'])) {
    $std_id = $_GET['delete_id'];
    $sql = "DELETE FROM students WHERE std_id='$std_id'";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        echo "Record deleted successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    header('Location: read.php'); // redirect back to read.php
    exit;
}

// Fetch existing student records with pagination

$limit = 5; // number of records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$sql = "SELECT * FROM students LIMIT $offset, $limit";
$result = $conn->query($sql);

// count total number of records
$total_records = $conn->query("SELECT COUNT(*) as total FROM students")->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Read Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
     <script src=JQuery.js></script>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Students List</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Date of Birth</th>
                <th>Phone Number</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><?php echo $row['std_id']; ?></td>
                        <td><?php echo $row['firstname']; ?></td>
                        <td><?php echo $row['lastname']; ?></td>
                        <td><?php echo $row['dob']; ?></td>
                        <td><?php echo $row['phoneno']; ?></td>
                        <td><?php echo $row['address']; ?></td>
                        <td>
                         <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" 
                           onclick="fetchRecord(<?php echo $row['std_id']; ?>)"
                                data-id="<?php echo $row['std_id']; ?>"
                                data-firstname="<?php echo $row['firstname']; ?>"
                                data-lastname="<?php echo $row['lastname']; ?>"
                                data-dob="<?php echo $row['dob']; ?>"
                                data-phoneno="<?php echo $row['phoneno']; ?>"
                                data-address="<?php echo $row['address']; ?>"
                            >Edit</button>
                            <a class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $row['std_id']; ?>)">Delete</a>
                             
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='7' class='text-center'>No students found</td></tr>";
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" class="text-center">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">Add</button>
                </td>
            </tr>
        </tfoot>
    </table>
    <!-- Pagination -->
    <nav>
        <ul class="pagination justify-content-center">
            <?php
            if ($page > 1) {
                echo '<li class="page-item"><a class="page-link" href="read.php?page=' . ($page - 1) . '">Previous</a></li>';
            }
            for ($i = 1; $i <= $total_pages; $i++) {
                $active = $i == $page ? 'active' : '';
                echo '<li class="page-item ' . $active . '"><a class="page-link" href="read.php?page=' . $i . '">' . $i . '</a></li>';
            }
            if ($page < $total_pages) {
                echo '<li class="page-item"><a class="page-link" href="read.php?page=' . ($page + 1) . '">Next</a></li>';
            }
            ?>
        </ul>
    </nav>

</div>

<!-- Add Modal -->
<form onsubmit="return validateForm()" method="POST">
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addModalLabel">Add New Student</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="firstname" class="form-label">First Name</label>
                        <input type="text" name="firstname" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="lastname" class="form-label">Last Name</label>
                        <input type="text" name="lastname" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="dob" class="form-label">Date of Birth</label>
                        <input type="date" name="dob" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="phoneno" class="form-label">Phone Number</label>
                        <input type="text" id="phoneno" name="phoneno" class="form-control" required>
                        <div id="phonenoErr" class="text-danger"></div>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" name="address" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary" name="add_student" value="Add">
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Update Modal -->
<form onsubmit="return validateForm()" method="POST">
    <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="updateModalLabel">Update Student</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="std_id" id="update_std_id">
                    <div class="mb-3">
                        <label for="firstname" class="form-label">First Name</label>
                        <input type="text" name="firstname" class="form-control" id="update_firstname" required>
                    </div>
                    <div class="mb-3">
                        <label for="lastname" class="form-label">Last Name</label>
                        <input type="text" name="lastname" class="form-control" id="update_lastname" required>
                    </div>
                    <div class="mb-3">
                        <label for="dob" class="form-label">Date of Birth</label>
                        <input type="date" name="dob" class="form-control" id="update_dob" required>
                    </div>
                    <div class="mb-3">
                        <label for="phoneno" class="form-label">Phone Number</label>
                        <input type="text" id="update_phoneno" name="phoneno" class="form-control" required>
                        <div id="phonenoErr" class="text-danger"></div>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" name="address" class="form-control" id="update_address" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary" name="update_student" value="Update">
                </div>
            </div>
        </div>
    </div>
</form>


<!-- Update Modal -->
<form action="" method="POST">
<div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="updateModalLabel">Update Student</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="std_id" id="update_std_id">
        <div class="mb-3">
            <label for="firstname" class="form-label">First Name</label>
            <input type="text" name="firstname" class="form-control" id="update_firstname" required>
        </div>
        <div class="mb-3">
            <label for="lastname" class="form-label">Last Name</label>
            <input type="text" name="lastname" class="form-control" id="update_lastname" required>
        </div>
        <div class="mb-3">
            <label for="dob" class="form-label">Date of Birth</label>
            <input type="date" name="dob" class="form-control" id="update_dob" required>
        </div>
        <div class="mb-3">
            <label for="phoneno" class="form-label">Phone Number</label>
            <input type="text" name="phoneno" class="form-control" id="update_phoneno" required>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <input type="text" name="address" class="form-control" id="update_address" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <input type="submit" class="btn btn-primary" name="update_student" value="Update">
      </div>
    </div>
  </div>
</div>
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
    function validateForm() {
        // Retrieving the values of form elements
        var phoneno = document.getElementById("phoneno").value;

        // Defining error variable with a default value
        var phonenoErr = true;

        // Validate phone number
        if (phoneno == "") {
            printError("phonenoErr", "Please enter your phone number");
            var elem = document.getElementById("phoneno");
            elem.classList.add("input-2");
            elem.classList.remove("input-1");
        } else {
            var regex = /^[1-9]\d{9}$/;
            if (regex.test(phoneno) === false) {
                printError("phonenoErr", "Please enter a valid 10 digit phone number");
                var elem = document.getElementById("phoneno");
                elem.classList.add("input-2");
                elem.classList.remove("input-1");
            } else {
                printError("phonenoErr", "");
                phonenoErr = false;
                var elem = document.getElementById("phoneno");
                elem.classList.add("input-1");
                elem.classList.remove("input-2");
            }
        }

        // Prevent the form from being submitted if there are any errors
        if (phonenoErr) {
            return false;
        } else {
            return true;
        }
    }

    // Function to print error message
    function printError(elemId, hintMsg) {
        document.getElementById(elemId).innerHTML = hintMsg;
    }
    // Fill update modal with existing data
    var updateModal = document.getElementById('updateModal');
    updateModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var firstname = button.getAttribute('data-firstname');
        var lastname = button.getAttribute('data-lastname');
        var dob = button.getAttribute('data-dob');
        var phoneno = button.getAttribute('data-phoneno');
        var address = button.getAttribute('data-address');

        var modal = this;
        modal.querySelector('#update_std_id').value = id;
        modal.querySelector('#update_firstname').value = firstname;
        modal.querySelector('#update_lastname').value = lastname;
        modal.querySelector('#update_dob').value = dob;
        modal.querySelector('#update_phoneno').value = phoneno;
        modal.querySelector('#update_address').value = address;
    });

    // Confirm before deleting a record
    function confirmDelete(id) {
        if (confirm("Are you sure you want to delete this record?")) {
            window.location.href = "read.php?delete_id=" + id;
        }
    }

    // Fetch record details via AJAX
    function fetchRecord(std_id) {
        $.ajax({
            type: "POST",
            url: "fetch_record.php",
            data: {std_id: std_id},
            success: function(response){
                var data = JSON.parse(response);
                // Assuming you want to update the modal fields here
                document.getElementById('update_std_id').value = data.std_id;
                document.getElementById('update_firstname').value = data.firstname;
                document.getElementById('update_lastname').value = data.lastname;
                document.getElementById('update_dob').value = data.dob;
                document.getElementById('update_phoneno').value = data.phoneno;
                document.getElementById('update_address').value = data.address;

                // Show the update modal
                var updateModal = new bootstrap.Modal(document.getElementById('updateModal'));
                updateModal.show();
            },
            error: function(xhr, status, error) {
                console.error("Error fetching record:", error);
                // Handle error here
            }
        });
    }
</script>

</body>
</html>
