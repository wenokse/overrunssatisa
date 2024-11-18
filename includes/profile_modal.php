<!-- Transaction History -->
<div class="modal fade" id="transaction">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><b>Transaction Full Details</b></h4>
            </div>
            <div class="modal-body table-responsive">
              <p>
                Date: <span id="date"></span>
                <span class="pull-right">Transaction #: <span id="transid"></span></span> 
              </p>
              <!-- Add Recipient Details Section -->
              <div class="well">
                        <div class="row">
                            <div class="col-md-6">
                                <h4><b>Delivery Information</b></h4>
                                <p><strong>Recipient Name:</strong> <span id="recipient"></span></p>
                                <p><strong>Delivery Address:</strong> <span id="delivery_address"></span></p>
                                <p><strong>Purok/Area:</strong> <span id="address2"></span></p>
                                <p><strong>Landmark:</strong> <span id="address3"></span></p>
                            </div>
                            <div class="col-md-6">
                                <h4><b>Rider Information</b></h4>
                                <p><strong>Rider Name:</strong> <span id="rider_name"></span></p>
                                <p><strong>Phone Number:</strong> <span id="phone_number"></span></p>
                                <p><strong>Address:</strong> <span id="rider_address"></span></p>
                            </div>
                        </div>
                    </div>
              <table class="table table-bordered">
                <thead>
                <th>Product Image</th>
                <th>Name</th>
                <th>Price</th>
                <th>Size</th>
                <th>Color</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Action</th>
                </thead>
                <tbody id="detail">
                </tbody>
              </table>
              <div class="pull-right">
              <p style="color: red;">Shipping: 100</p><p><b>Total Amount: <span id="total"></span></b></p>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content MODAL-IMG">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: red;">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img src="" id="modalImage" class="img-responsive center-block" style="max-height: 70vh;">
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile -->
<div class="modal fade" id="edit">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><b>Update Account</b></h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" method="POST" action="profile_edit" enctype="multipart/form-data" onsubmit="return validatePhoneNumber()">
                <div class="form-group">
                    <label for="firstname" class="col-sm-3 control-label">Firstname</label>

                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo $user['firstname']; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="lastname" class="col-sm-3 control-label">Lastname</label>

                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo $user['lastname']; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="email" class="col-sm-3 control-label">Email</label>

                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="password" class="col-sm-3 control-label">Password</label>

                    <div class="col-sm-9">
                      <input type="password" class="form-control" id="password" name="password" value="<?php echo $user['password']; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="contact" class="col-sm-3 control-label">Contact No</label>

                    <div class="col-sm-9">
                      <input type="tel" class="form-control" id="contact_info" name="contact_info" value="<?php echo $user['contact_info']; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="address" class="col-sm-3 control-label">Address</label>

                    <div class="col-sm-9">
                      <input class="form-control" id="address" name="address" value="<?php echo $user['address']; ?>"></input>
                    </div>
                </div>
                <div class="form-group">
                    <label for="address2" class="col-sm-3 control-label">Purok</label>

                    <div class="col-sm-9">
                      <input class="form-control" id="address2" name="address2" value="<?php echo $user['address2']; ?>"></input>
                    </div>
                </div>
                <div class="form-group">
                    <label for="photo" class="col-sm-3 control-label">Photo</label>

                    <div class="col-sm-9">
                      <input type="file" id="photo" name="photo">
                    </div>
                </div>
                <hr>
                
                <div class="form-group">
                    <label for="curr_password" class="col-sm-3 control-label">Current Password</label>

                    <div class="col-sm-9">
                      <input type="password" class="form-control" id="curr_password" name="curr_password" placeholder="input current password to save changes" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
              <button type="submit" class="btn btn-success btn-flat" name="edit"><i class="fa fa-check-square-o"></i> Update</button>
              </form>
            </div>
        </div>
    </div>
</div>
<script>

// Add this to your existing script section
document.getElementById('photo').addEventListener('change', function() {
    const file = this.files[0];
    const fileSize = file.size / 1024 / 1024; // Convert to MB
    const fileType = file.type;
    
    // Check file size (5MB limit)
    if (fileSize > 5) {
        swal({
            title: 'File size must not exceed 5MB',
            icon: 'warning',
            button: 'OK'
        });
        this.value = ''; // Clear the file input
        return false;
    }
    
    // Check file type
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!allowedTypes.includes(fileType)) {
        swal({
            title: 'Only JPG, PNG & GIF files are allowed',
            icon: 'warning',
            button: 'OK'
        });
        this.value = ''; // Clear the file input
        return false;
    }
});

    // Restrict input for the phone number field to numbers only
    var input = document.getElementById('contact_info');
    input.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    function validatePhoneNumber() {
        var phoneNumber = document.getElementById('contact_info').value;
        if (phoneNumber.length !== 11) {
            swal({
                title: 'Phone number must be exactly 11 digits long.',
                icon: 'warning',
                button: 'OK'
            });
            return false;
        }
        return true;
    }

    function validateForm() {
        var email = document.getElementById('email').value.trim();
        var password = document.getElementById('password').value.trim();
        var firstname = document.getElementById('firstname').value.trim();
        var lastname = document.getElementById('lastname').value.trim();
        var address = document.getElementById('address').value.trim();
        var contact_info = document.getElementById('contact_info').value.trim();
        
        var specialChars = /[<>:\/\$;,?!]/;
        var hasNumber = /\d/;
        var hasUppercase = /[A-Z]/;
        var hasLowercase = /[a-z]/;

        // Check if any field is empty or consists of only spaces
        if (firstname === "" || lastname === "" || email === "" || password === "" || address === "" || contact_info === "") {
            swal({
                title: 'Please fill out all required fields properly.',
                icon: 'warning',
                button: 'OK'
            });
            return false;
        }

        // Validate email domain
        if (!email.endsWith("@gmail.com")) {
            swal({
                title: 'Email must be a @gmail.com address.',
                icon: 'warning',
                button: 'OK'
            });
            return false;
        }

        // Validate password strength
    if (password.length < 8) {
        swal({
            title: 'Password must be at least 8 characters long.',
            icon: 'warning',
            button: 'OK'
        });
        return false;
    }

        // Validate password strength
        if (!hasNumber.test(password) || !hasUppercase.test(password) || !hasLowercase.test(password) || specialChars.test(password)) {
            swal({
                title: 'Password must contain at least one uppercase letter, one lowercase letter, one number, and no special characters.',
                icon: 'warning',
                button: 'OK'
            });
            return false;
        }

        // Restrict special characters in first/last name, and address
        if (specialChars.test(firstname) || specialChars.test(lastname) || specialChars.test(address) || specialChars.test(address2)) {
            swal({
                title: 'Special characters not allowed.',
                icon: 'warning',
                button: 'OK'
            });
            return false;
        }

        return validatePhoneNumber();
    }

    document.querySelector('form').addEventListener('submit', validateForm);
</script>
