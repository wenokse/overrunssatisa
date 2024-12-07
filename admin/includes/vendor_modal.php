<!-- View Modal -->
<div class="modal fade" id="view">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><b>Vendor Details</b></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <img id="view_photo" src="" class="img-responsive center-block" style="max-width: 200px; max-height: 200px;">
                    </div>
                    <div class="col-md-6">
                        <h3 id="view_fullname"></h3>
                        <p><strong>Email:</strong> <span id="view_email"></span></p>
                        <p><strong>Contact:</strong> <span id="view_contact"></span>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <h4>Address Information</h4>
                        <p><strong>Primary Address:</strong> <span id="view_address"></span></p>
                        <p><strong>Secondary Address:</strong> <span id="view_address2"></span></p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <h4>Login Location Details</h4>
                        <p><strong>Address:</strong> <span id="fetched_address">Fetching...</span></p>
                        <p><strong>Latitude:</strong> <span id="view_latitude">N/A</span></p>
                        <p><strong>Longitude:</strong> <span id="view_longitude">N/A</span></p>
                        <div id="location_trace_btn" class="text-center mt-3" style="display:none;">
                            <button class="btn btn-primary" onclick="traceUserLocation()">
                                <i class="fa fa-map-marker"></i> Trace Location
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
            </div>
        </div>
    </div>
</div>


<!-- Add -->
<div class="modal fade" id="addnewvendor">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><b>Add New Vendor</b></h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST" action="vendor_add" enctype="multipart/form-data" onsubmit="return validatePhoneNumber()">
                    <div class="form-group">
                        <label for="firstname" class="col-sm-3 control-label">Firstname</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="firstname" name="firstname" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="lastname" class="col-sm-3 control-label">Lastname</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="lastname" name="lastname" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="store" class="col-sm-3 control-label">Name of Store</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="store" name="store" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="contact_info" class="col-sm-3 control-label">Phone Number</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="contact_info" id="contact_info" maxlength="11" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="municipality" class="col-sm-3 control-label">Address of Store</label>
                        <div class="col-sm-9">
                            <select id="municipality" class="form-control" name="municipality" required>
                                <option value="">Select Municipality</option>
                                <option value="Bantayan">Bantayan</option>
                                <option value="Madridejos">Madridejos</option>
                                <option value="Santa Fe">Santa Fe</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="barangay" class="col-sm-3 control-label">Barangay</label>
                        <div class="col-sm-9">
                            <select id="barangay" class="form-control" name="barangay" required>
                                <option value="">Select Barangay</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address" class="col-sm-3 control-label">Address</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="address" id="address"  required readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address2" class="col-sm-3 control-label">Purok</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="address2" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="col-sm-3 control-label">Email</label>
                        <div class="col-sm-9">
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password" class="col-sm-3 control-label">Password</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <span class="input-group-addon">
                                    <input type="checkbox" id="togglePassword">
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="retypepassword" class="col-sm-3 control-label">Retype Password</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="repassword" name="repassword" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="photo" class="col-sm-3 control-label">Photo</label>
                        <div class="col-sm-9">
                            <input type="file" id="photo" name="photo">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
                        <button type="submit" class="btn btn-primary btn-flat" name="add"><i class="fa fa-save"></i> Save</button>
                    </div>
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

    document.getElementById('togglePassword').addEventListener('change', function() {
        const passwordField = document.getElementById('password');
        passwordField.type = this.checked ? 'text' : 'password';
    });

    const barangays = {
        'Bantayan': ['Atop-atop', 'Baigad', 'Bantigue', 'Baod', 'Binaobao', 'Guiwanon', 'Hilotongan', 'Kabac', 'Kabangbang', 'Kampingganon', 'Kangkaibe', 'Lipayran', 'Luyongbaybay', 'Mojon', 'Obo-ob', 'Patao', 'Putian', 'Sillon', 'Suba', 'Sulangan', 'Sungko', 'Tamiao', 'Ticad'],
        'Madridejos': ['Bunakan', 'Kangwayan', 'Kaongkod', 'Kodia', 'Maalat', 'Malbago', 'Mancilang', 'Pili', 'Poblacion', 'San Agustin', 'Talangnan', 'Tarong', 'Tugas', 'Tabagak'],
        'Santa Fe': ['Balidbid', 'Hagdan', 'Hilantagaan', 'Kinatarkan', 'Langub', 'Maricaban', 'Okoy', 'Poblacion', 'Pooc', 'Talisay']
    };

    document.getElementById('municipality').addEventListener('change', function() {
        const selectedMunicipality = this.value;
        const barangaySelect = document.getElementById('barangay');
        barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
        
        if (barangays[selectedMunicipality]) {
            barangays[selectedMunicipality].forEach(function(barangay) {
                const option = document.createElement('option');
                option.value = barangay;
                option.textContent = barangay;
                barangaySelect.appendChild(option);
            });
        }
    });

    document.getElementById('barangay').addEventListener('change', function() {
        const selectedMunicipality = document.getElementById('municipality').value;
        const selectedBarangay = this.value;
        document.getElementById('address').value = selectedMunicipality + ', ' + selectedBarangay;
    });

    
</script>

<!-- Edit Customer -->
<div class="modal fade" id="edit">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><b>Edit Vendor</b></h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" method="POST" action="vendor_edit">
                <input type="hidden" class="userid" name="id">
                <div class="form-group">
                    <label for="edit_firstname" class="col-sm-3 control-label">Firstname</label>

                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="edit_firstname" name="firstname" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit_lastname" class="col-sm-3 control-label">Lastname</label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="edit_lastname" name="lastname" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit_contact" class="col-sm-3 control-label">Contact</label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="edit_contact"  maxlength="11" name="contact" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit_store" class="col-sm-3 control-label">Name of Store</label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="edit_store" name="store" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit_email" class="col-sm-3 control-label">Email</label>

                    <div class="col-sm-9">
                      <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit_password" class="col-sm-3 control-label">Password</label>

                    <div class="col-sm-9">
                      <input type="password" class="form-control" id="edit_password" name="password" required>
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

<!-- Delete -->
<div class="modal fade" id="delete">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><b>Deleting...</b></h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" method="POST" action="vendor_delete">
                <input type="hidden" class="userid" name="id">
                <div class="text-center">
                    <p>DELETE VENDOR</p>
                    <h2 class="bold fullname"></h2>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
              <button type="submit" class="btn btn-danger btn-flat" name="delete"><i class="fa fa-trash"></i> Delete</button>
              </form>
            </div>
        </div>
    </div>
</div>

<!-- Update Photo -->
<div class="modal fade" id="edit_photo">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><b><span class="fullname"></span></b></h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" method="POST" action="vendor_photo" enctype="multipart/form-data">
                <input type="hidden" class="userid" name="id">
                <div class="form-group">
                    <label for="photo" class="col-sm-3 control-label">Photo</label>

                    <div class="col-sm-9">
                      <input type="file" id="photo" name="photo" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
              <button type="submit" class="btn btn-success btn-flat" name="upload"><i class="fa fa-check-square-o"></i> Update</button>
              </form>
            </div>
        </div>
    </div>
</div> 

<!-- Activate -->
<div class="modal fade" id="activate">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><b>Activating...</b></h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" method="POST" action="vendor_activate">
                <input type="hidden" class="userid" name="id">
                <div class="text-center">
                    <p>ACTIVATE USER</p>
                    <h2 class="bold fullname"></h2>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
              <button type="submit" class="btn btn-success btn-flat" name="activate"><i class="fa fa-check"></i> Activate</button>
              </form>
            </div>
        </div>
    </div>
</div>

<!-- Deactivate -->
<div class="modal fade" id="deactivate">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><b>Deactivating...</b></h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" method="POST" action="vendor_deactivate">
                <input type="hidden" class="userid" name="id">
                <div class="text-center">
                    <p>DEACTIVATE USER</p>
                    <h2 class="bold fullname"></h2>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
              <button type="submit" class="btn btn-danger btn-flat" name="deactivate"><i class="fa fa-check"></i> Deactivate</button>
              </form>
            </div>
        </div>
    </div>
</div>
<!-- decline modal -->
<div class="modal fade" id="declineModal" tabindex="-1" role="dialog" aria-labelledby="declineModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="declineModalLabel">Decline Vendor</h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" id="declineForm">
          <input type="hidden" id="decline_vendor_id" name="id">
          <div class="form-group">
            <label for="decline_message" class="col-sm-3 control-label">Reason for Decline</label>
            <div class="col-sm-9">
              <textarea class="form-control" id="decline_message" name="message" rows="4" required></textarea>
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-danger btn-flat" id="declineSubmit">Send</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="viewDocuments">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title"><b>Vendor Documents</b></h4>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th width="20%">Document Type</th>
                <th width="40%">Actions</th>
                <th width="40%">Preview</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>BIR Document</td>
                <td>
                  <!-- <button type="button" class="btn btn-info btn-sm btn-flat view-doc" data-type="bir_doc">
                    <i class="fa fa-eye"></i> View
                  </button> -->
                  <a href="#" class="btn btn-primary btn-sm btn-flat download-doc" data-type="bir_doc">
                    <i class="fa fa-download"></i> Download
                  </a>
                </td>
                <td>
                  <div class="doc-preview" id="preview_bir_doc">
                    <span class="text-muted">Loading...</span>
                  </div>
                </td>
              </tr>
              <tr>
                <td>DTI Document</td>
                <td>
                  <!-- <button type="button" class="btn btn-info btn-sm btn-flat view-doc" data-type="dti_doc">
                    <i class="fa fa-eye"></i> View
                  </button> -->
                  <a href="#" class="btn btn-primary btn-sm btn-flat download-doc" data-type="dti_doc">
                    <i class="fa fa-download"></i> Download
                  </a>
                </td>
                <td>
                  <div class="doc-preview" id="preview_dti_doc">
                    <span class="text-muted">Loading...</span>
                  </div>
                </td>
              </tr>
              <tr>
                <td>Mayor's Permit</td>
                <td>
                  <!-- <button type="button" class="btn btn-info btn-sm btn-flat view-doc" data-type="mayor_permit">
                    <i class="fa fa-eye"></i> View
                  </button> -->
                  <a href="#" class="btn btn-primary btn-sm btn-flat download-doc" data-type="mayor_permit">
                    <i class="fa fa-download"></i> Download
                  </a>
                </td>
                <td>
                  <div class="doc-preview" id="preview_mayor_permit">
                    <span class="text-muted">Loading...</span>
                  </div>
                </td>
              </tr>
              
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<style>
.doc-preview {
    width: 100px;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 5px;
    background-color: #f9f9f9;
}

.doc-preview img {
    object-fit: contain;
    width: 100%;
    height: 100%;
}

.doc-preview .fa-file-pdf-o {
    color: #dc3545;
}

.doc-preview .text-danger {
    font-size: 12px;
    text-align: center;
}

.disabled {
    pointer-events: none;
    opacity: 0.6;
    cursor: not-allowed;
}
#documentViewer {
    min-height: 400px;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 15px;
}

.btn-sm {
    margin: 2px;
}
</style>
