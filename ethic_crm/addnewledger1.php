<?php
    include_once("connect.php");
?>
<table class='table'>
    <tr>
        <th>Ledger Name</th>
        <td>                                            
            <div class="input-group">
                <span class="input-group-addon"><span class="fa fa-user"></span></span>
                <input type="text" class="form-control" id="lname" required onkeypress="return onlyCharacters(event);"/>
            </div>                                            
        </td>
        <th>Contact Person</th>
        <td>                                            
            <div class="input-group">
                <span class="input-group-addon"><span class="fa fa-user"></span></span>
                <input type="text" class="form-control" id="cperson"  onkeypress="return onlyCharacters(event);"/>
            </div>                                            
        </td>
    </tr>
    <tr>
        <th>Mobile No.</th>
        <td>
            <div class="input-group">
                <span class="input-group-addon"><span class="fa fa-mobile"></span></span> 
                <input type="text" class="form-control" id="mobile" onkeyup="return allowOnly10Numeric(this);" oninput="allowOnly10Numeric(this);">
                <span id="mobileError" style="color: red; font-size: 14px;"></span>
            </div>
        </td>
        <th>Email ID</th>
        <td>                                            
            <div class="input-group">
                <span class="input-group-addon"><span class="fa fa-envelope"></span></span>
                <input type="email" class="form-control" id="email" />
            </div>                                            
        </td>
    </tR>
    <tr>
        <th>Address</th>
        <td>                                            
            <textarea class="form-control" rows="5" id="address"></textarea>
        </td>
    </tr>
</table>