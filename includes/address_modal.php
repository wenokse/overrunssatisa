<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1" role="dialog" aria-labelledby="addressModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="addressModal">Add Delivery Address</h4>
            </div>
            <form id="addressForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="recipient_name">Recipient Name</label>
                        <input type="text" class="form-control" id="recipient_name" name="recipient_name" required>
                    </div>
                    <div class="form-group">
    <label for="phone">Phone Number</label>
    <input type="tel" class="form-control" id="phone" name="phone" required pattern="\d{11}" maxlength="11" oninput="this.value=this.value.replace(/[^0-9]/g,'');">
    <small class="form-text text-muted">Please enter exactly 11 digits (e.g., 09123456789)</small>
</div>

                    <div class="form-group">
                        <label for="address">Municipal, Barangay</label>
                        <input type="text" class="form-control" id="address" name="address" required>
                    </div>
                    <div class="form-group">
                        <label for="address2">Purok</label>
                        <input type="text" class="form-control" id="address2" name="address2" required>
                    </div>
                    <div class="form-group">
                        <label for="address3">Address2</label>
                        <input type="text" class="form-control" id="address3" name="address3" required>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Address</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="termsModal" class="modal2">
    <div class="modal-content2">
        <span class="close1">&times;</span>
        <h2>Terms and Conditions</h2>
        <div class="modal-body">
            <h3>1. Products and Services</h3>
            <p>We strive to provide accurate descriptions of our products, but we do not guarantee that any description is complete, current, or free of errors. Product availability and prices are subject to change without notice.</p>

            <h3>2. Orders and Payments</h3>
            <p>All prices are in [Currency]. We reserve the right to refuse or cancel any order due to pricing errors, stock issues, or potential fraud. Payment must be completed before the shipment of goods.</p>

            <h3>3. Returns and Refunds</h3>
                <p>If the item you receive does not match the description or specifications of the ordered item, you may return it within 3 to 4 days of receipt. However, please ensure the following:</p>
                <ul>
                    <li>The product is in its original condition and packaging.</li>
                    <li>You provide a video recording as proof, clearly showing the item as it was received, including the unboxing, so we can verify any discrepancies.</li>
                    <li>Opened items or items that show signs of use may not be eligible for return or refund.</li>
                </ul>
                <p>Please note that shipping fees will be covered by the customer and are non-refundable. If the return request meets these criteria, we will process the return and issue a refund or replacement as appropriate.</p>

            <h3>4. Pricing </h3>
            <p>Prices listed on our site are subject to change without notice.</p>

            <h3>5. User Conduct</h3>
            <p>Users agree to use the site only for lawful purposes and in a manner that does not infringe on the rights of others or restrict the use of the site. Prohibited activities include, but are not limited to, harassment, defamation, and uploading viruses or harmful code.</p>

            <h3>6. Intellectual Property</h3>
            <p>All content on this site, including text, images, logos, and designs, are owned by or licensed to Overruns Sa Tisa Online Shop and are protected by intellectual property laws. Unauthorized use of this content is prohibited.</p>

            <h3>7. Limitation of Liability</h3>
            <p>We do not warrant that the use of our service will be uninterrupted, timely, or error-free. In no case shall Overruns Sa Tisa Online Shop shall be liable for any injury, loss, claim, or any direct or indirect damages resulting from your use of our website.</p>

            <h3>8. Governing Law</h3>
            <p>These Terms and any separate agreements shall be governed by and construed in accordance with the laws of Philippines.</p>

            <h3>9. Amendments</h3>
            <p>We reserve the right to modify these terms at any time. Changes will be effective immediately upon posting on the website.</p>

            <h3>10. Contact Information</h3>
            <p>If you have any questions about these Terms and Conditions, please contact us at rowensecuya25@gmail.com.</p>
        </div>
    </div>
</div>
<style>
    /* Modal styles */
    .modal2 {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.4);
    }

    .modal-content2 {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 700px;
        max-height: 80vh;
        overflow-y: auto;
        border-radius: 25px;
    }

    .close1 {
        color: black;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close1:hover,
    .close1:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
    .modal-content h2 {
        margin-top: 0;
        padding-bottom: 10px;
        border-bottom: 1px solid #ddd;
    }

    .modal-body {
        margin-top: 20px;
    }
</style>
<!-- OTP Verification Modal -->
<div class="modal fade" id="otpModal" tabindex="-1" role="dialog" aria-labelledby="otpModalLabel" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="otpModalLabel">Verify OTP</h4>
            </div>
            <div class="modal-body">
                <form id="otpForm">
                    <div class="form-group">
                        <label for="otp">Enter OTP</label>
                        <input type="text" class="form-control" id="otp" name="otp" required maxlength="6">
                    </div>
                    <button type="submit" class="btn btn-primary">Verify</button>
                </form>
                <div class="text-center mt-3">
                    <button id="resendOtpBtn" class="btn btn-link">Didn't get a code? Resend</button>
                </div>
            </div>
        </div>
    </div>
</div>
