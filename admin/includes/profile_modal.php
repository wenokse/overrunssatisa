<!-- Add -->
<div class="modal fade" id="profile">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><b>
                <?php if ($admin['type'] == 1): ?>
                <b>Admin Profile</b>
                <?php else: ?>
                <b>Vendor Profile</b>
                <?php endif; ?></b></h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST" action="profile_update" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="email" class="col-sm-3 control-label">Email</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="email" name="email" value="<?php echo $admin['email']; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password" class="col-sm-3 control-label">Password</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="password" name="password" value="<?php echo $admin['password']; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="firstname" class="col-sm-3 control-label">Firstname</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo $admin['firstname']; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="lastname" class="col-sm-3 control-label">Lastname</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo $admin['lastname']; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="contact_info" class="col-sm-3 control-label">Contact Number</label>
                        <div class="col-sm-9">
                            <input type="text" 
                                class="form-control" 
                                id="contact_info" 
                                name="contact_info" 
                                value="<?php echo $admin['contact_info']; ?>" 
                                maxlength="11" 
                                pattern="09\d{9}" 
                                title="Contact number must start with '09' and be 11 digits long."
                                oninput="validateContactNumber(this)">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="photo" class="col-sm-3 control-label">Photo</label>
                        <div class="col-sm-9">
                            <input type="file" id="photo" name="photo" accept="image/png, image/jpeg, image/jpg">
                            <img id="photo-preview" src="#" alt="Your Image" style="display:none; border-radius: 50%; width: 100px; height: 100px; margin-top: 10px;">
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="curr_password" class="col-sm-3 control-label">Current Password:</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="curr_password" name="curr_password" placeholder="input current password to save changes" required>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
                <button type="submit" class="btn btn-success btn-flat" name="save"><i class="fa fa-check-square-o"></i> Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('photo').onchange = function(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var preview = document.getElementById('photo-preview');
            preview.src = reader.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    };
    function validateContactNumber(input) {
        input.value = input.value.replace(/[^0-9]/g, '');
        if (!input.value.startsWith('09')) {
            input.value = '09';
        }
        if (input.value.length > 11) {
            input.value = input.value.slice(0, 11);
        }
    }
</script>
