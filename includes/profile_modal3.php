<!-- Transaction History -->
<div class="modal fade" id="transaction">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title"><b>Transaction Full Details</b></h4>
      </div>
      <div class="modal-body table-responsive">
        <div class="customer-details" style="background: #fff; padding: 15px; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
          <div class="row">
            <div class="col-md-6">
            <div class="detail-group" style="margin-bottom: 10px;">
                <label style="font-weight: bold; color: #555;">Date:</label>
                <span id="date" style="margin-left: 5px;"></span>
              </div>
              <div class="detail-group" style="margin-bottom: 10px;">
                <label style="font-weight: bold; color: #555;">Customer Name:</label>
                <span id="name" style="margin-left: 5px;"></span>
              </div>
              <div class="detail-group" style="margin-bottom: 10px;">
                <label style="font-weight: bold; color: #555;">Delivery Address:</label>
                <span style="margin-left: 5px;">
                  <span id="address"></span>,
                  <span id="address2"></span>,
                  <span id="address3"></span>
                </span>
              </div>
              <div class="detail-group">
                <label style="font-weight: bold; color: #555;">Contact Number:</label>
                <span id="contact_info" style="margin-left: 5px;"></span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="detail-group" style="margin-bottom: 10px;">
                <label style="font-weight: bold; color: #555;">Transaction #:</label>
                <span id="transid" style="margin-left: 5px;"></span>
              </div>
              <div class="detail-group" style="margin-bottom: 10px;">
                <label style="font-weight: bold; color: #555;">Rider Name:</label>
                <span id="rider_name" style="margin-left: 5px;"></span>
              </div>
              <div class="detail-group" style="margin-bottom: 10px;">
                <label style="font-weight: bold; color: #555;">Rider Contact:</label>
                <span id="phone_number" style="margin-left: 5px;"></span>
              </div>
              <div class="detail-group">
                <label style="font-weight: bold; color: #555;">Rider Address:</label>
                <span id="rider_address" style="margin-left: 5px;"></span>
              </div>
            </div>
          </div>
        </div>

        <div class="order-details" style="background: #fff; padding: 15px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
          <table class="table table-bordered" style="margin-bottom: 0;">
            <thead style="background: #f8f9fa;">
              <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Size</th>
                <th>Color</th>
                <th class="text-center">Quantity</th>
                <th>Subtotal</th>
              </tr>
            </thead>
            <tbody id="detail">
              <tr>
              <td style="font-weight: bold; background: #f8f9fa;">Shipping: 100</td>
              <td colspan="4" class="text-right" style="font-weight: bold; background: #f8f9fa;">Total</td>
                <td style="background: #f8f9fa;"><span id="total" style="font-weight: bold;"></span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer" style="background: #f8f9fa; border-top: 2px solid #dee2e6;">
        <button type="button" class="btn btn-default btn-flat" data-dismiss="modal" style="border-radius: 3px;">
          <i class="fa fa-close"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>

<style>
.modal-dialog.modal-lg {
  width: 90%;
  max-width: 1000px;
}

.modal-content {
  border-radius: 8px;
  border: none;
}

.modal-header {
  padding: 15px 20px;
}

.modal-body {
  padding: 20px;
}

.table-bordered {
  border: 1px solid #dee2e6;
}

.table-bordered > thead > tr > th,
.table-bordered > tbody > tr > td {
  padding: 12px;
  vertical-align: middle;
  border: 1px solid #dee2e6;
}

.detail-group {
  display: flex;
  align-items: baseline;
}

.customer-details,
.order-details {
  transition: box-shadow 0.3s ease;
}

.customer-details:hover,
.order-details:hover {
  box-shadow: 0 3px 6px rgba(0,0,0,0.15);
}

@media (max-width: 768px) {
  .modal-dialog.modal-lg {
    width: 95%;
    margin: 10px auto;
  }
  
  .detail-group {
    margin-bottom: 15px;
  }
  
  .col-md-6:first-child {
    margin-bottom: 20px;
  }
}
</style>
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
                      <textarea class="form-control" id="address" name="address"><?php echo $user['address']; ?></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="address2" class="col-sm-3 control-label">Purok</label>

                    <div class="col-sm-9">
                      <textarea class="form-control" id="address2" name="address2"><?php echo $user['address2']; ?></textarea>
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
</script>