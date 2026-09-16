<?php
$page_title = "SAML Login Details";
include_once '/var/www/html/sp/shared/header.php';
include_once '/var/www/html/sp/shared/logout.php';
?>

<h2 class="garr-text-warning mb-4 text-center">SAML Login Details</h2>

<br>
<div class="w-75 mx-auto">
    <h4 class="garr-text-warning mb-4">Metadata</h4>
    <table class="table table-bordered table-striped" style="table-layout: fixed; width: 100%">
        <thead>
            <tr>
                <th class="garr-bar text-white" style="width: 35%">Name</th>
                <th class="garr-bar text-white" style="width: 65%">Value</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($_SERVER as $key => $value) {
                    if(strpos($key, 'Meta-') === 0) {
                        // Removing prefix
                        $clean_key = substr($key, 5);
                        echo "<tr><td class=\"text-break\"><strong>{$clean_key}</strong><td class=\"cell text-break\">" . htmlspecialchars($value) . "</td></tr>";
                    }
                }
            ?>
        </tbody>
    </table>
</div>

<div class="w-75 mx-auto">
    <br>
    <h4 class="garr-text-warning mb-4">Attributes</h4>
    <table class="table table-bordered table-striped" style="table-layout: fixed; width: 100%">
        <thead>
            <tr>
                <th class="garr-bar text-white" style="width: 35%">Attribute</th>
                <th class="garr-bar text-white" style="width: 65%">Value</th>
            </tr>
        </thead>
        <tbody>
            <?php
                // Mapping of common attributes to URNs
                $attributeUrnMap = [
                    // persistent IDs
                    "subject-id" => "urn:oasis:names:tc:SAML:attribute:subject-id",
                    "pairwise-id" => "urn:oasis:names:tc:SAML:attribute:pairwise-id",
                    "persistent-id" => "urn:oasis:names:tc:SAML:2.0:nameid-format:persistent",
                    "transient-id" => "urn:oasis:names:tc:SAML:2.0:nameid-format:transient",

                    // eduPerson Schema
                    "eduPersonAffiliation" => "urn:oid:1.3.6.1.4.1.5923.1.1.1.1",
                    "eduPersonNickname" => "urn:oid:1.3.6.1.4.1.5923.1.1.1.2",
                    "eduPersonOrgDN" => "urn:oid:1.3.6.1.4.1.5923.1.1.1.3",
                    "eduPersonOrgUnitDN" => "urn:oid:1.3.6.1.4.1.5923.1.1.1.4",
                    "eduPersonPrimaryAffiliation" => "urn:oid:1.3.6.1.4.1.5923.1.1.1.5",
                    "eduPersonPrincipalName" => "urn:oid:1.3.6.1.4.1.5923.1.1.1.6",
                    "eduPersonPrincipalNamePrior" => "urn:oid:1.3.6.1.4.1.5923.1.1.1.12",
                    "eduPersonEntitlement" => "urn:oid:1.3.6.1.4.1.5923.1.1.1.7",
                    "eduPersonPrimaryOrgUnitDN" => "urn:oid:1.3.6.1.4.1.5923.1.1.1.8",
                    "eduPersonScopedAffiliation" => "urn:oid:1.3.6.1.4.1.5923.1.1.1.9",
                    "eduPersonTargetedID" => "urn:oid:1.3.6.1.4.1.5923.1.1.1.10",
                    "eduPersonAssurance" => "urn:oid:1.3.6.1.4.1.5923.1.1.1.11",
                    "eduPersonUniqueId" => "urn:oid:1.3.6.1.4.1.5923.1.1.1.13",
                    "eduPersonOrcid" => "urn:oid:1.3.6.1.4.1.5923.1.1.1.16",
                    "eduPersonAnalyticsTag" => "urn:oid:1.3.6.1.4.1.5923.1.1.1.17",
                    "eduPersonDisplayPronouns" => "urn:oid:1.3.6.1.4.1.5923.1.1.1.18",

                    // schac Schema
                    "schacMotherTongue" => "urn:oid:1.3.6.1.4.1.25178.1.2.1",
                    "schacGender" => "urn:oid:1.3.6.1.4.1.25178.1.2.2",
                    "schacDateOfBirth" => "urn:oid:1.3.6.1.4.1.25178.1.2.3",
                    "schacPlaceOfBirth" => "urn:oid:1.3.6.1.4.1.25178.1.2.4",
                    "schacCountryOfCitizenship" => "urn:oid:1.3.6.1.4.1.25178.1.2.5",
                    "schacSn1" => "urn:oid:1.3.6.1.4.1.25178.1.2.6",
                    "schacSn2" => "urn:oid:1.3.6.1.4.1.25178.1.2.7",
                    "schacPersonalTitle" => "urn:oid:1.3.6.1.4.1.25178.1.2.8",
                    "schacHomeOrganization" => "urn:oid:1.3.6.1.4.1.25178.1.2.9",
                    "schacHomeOrganizationType" => "urn:oid:1.3.6.1.4.1.25178.1.2.10",
                    "schacCountryOfResidence" => "urn:oid:1.3.6.1.4.1.25178.1.2.11",
                    "schacUserPresenceID" => "urn:oid:1.3.6.1.4.1.25178.1.2.12",
                    "schacPersonalPosition" => "urn:oid:1.3.6.1.4.1.25178.1.2.13",
                    "schacPersonalUniqueCode" => "urn:oid:1.3.6.1.4.1.25178.1.2.14",
                    "schacPersonalUniqueID" => "urn:oid:1.3.6.1.4.1.25178.1.2.15",
                    "schacExpiryDate" => "urn:oid:1.3.6.1.4.1.25178.1.2.17",
                    "schacUserPrivateAttribute" => "urn:oid:1.3.6.1.4.1.25178.1.2.18",
                    "schacUserStatus" => "urn:oid:1.3.6.1.4.1.25178.1.2.19",
                    "schacProjectMembership" => "urn:oid:1.3.6.1.4.1.25178.1.2.20",
                    "schacProjectSpecificRole" => "urn:oid:1.3.6.1.4.1.25178.1.2.21",
                    "schacYearOfBirth" => "urn:oid:1.3.6.1.4.1.25178.1.0.2.3",
                    
                    // Other Common Person Attributes
                    "audio" => "urn:oid:0.9.2342.19200300.100.1.55",
                    "businessCategory" => "urn:oid:2.5.4.15",
                    "carLicense" => "urn:oid:2.16.840.1.113730.3.1.1",
                    "cn" => "urn:oid:2.5.4.3",
                    "departmentNumber" => "urn:oid:2.16.840.1.113730.3.1.2",
                    "description" => "urn:oid:2.5.4.13",
                    "displayName" => "urn:oid:2.16.840.1.113730.3.1.241",
                    "eduCourseOffering" => "urn:oid:1.3.6.1.4.1.5923.1.6.1.1",
                    "eduCourseMember" => "urn:oid:1.3.6.1.4.1.5923.1.6.1.2",
                    "employeeNumber" => "urn:oid:2.16.840.1.113730.3.1.3",
                    "employeeType" => "urn:oid:2.16.840.1.113730.3.1.4",
                    "facsimileTelephoneNumber" => "urn:oid:2.5.4.23",
                    "givenName" => "urn:oid:2.5.4.42",
                    "homePhone" => "urn:oid:0.9.2342.19200300.100.1.20",
                    "homePostalAddress" => "urn:oid:0.9.2342.19200300.100.1.39",
                    "initials" => "urn:oid:2.5.4.43",
                    "isMemberOf" => "urn:oid:1.3.6.1.4.1.5923.1.5.1.1",
                    "jpegPhoto" => "urn:oid:0.9.2342.19200300.100.1.60",
                    "l" => "urn:oid:2.5.4.7",
                    "labeledURI" => "urn:oid:1.3.6.1.4.1.250.1.57",
                    "mail" => "urn:oid:0.9.2342.19200300.100.1.3",
                    "manager" => "urn:oid:0.9.2342.19200300.100.1.10",
                    "mobile" => "urn:oid:0.9.2342.19200300.100.1.41",
                    "o" => "urn:oid:2.5.4.10",
                    "ou" => "urn:oid:2.5.4.11",
                    "pager" => "urn:oid:0.9.2342.19200300.100.1.42",
                    "physicalDeliveryOfficeName" => "urn:oid:2.5.4.19",
                    "postalAddress" => "urn:oid:2.5.4.16",
                    "postalCode" => "urn:oid:2.5.4.17",
                    "postOfficeBox" => "urn:oid:2.5.4.18",
                    "preferredLanguage" => "urn:oid:2.16.840.1.113730.3.1.39",
                    "seeAlso" => "urn:oid:2.5.4.34",
                    "sn" => "urn:oid:2.5.4.4",
                    "st" => "urn:oid:2.5.4.8",
                    "street" => "urn:oid:2.5.4.9",
                    "telephoneNumber" => "urn:oid:2.5.4.20",
                    "title" => "urn:oid:2.5.4.12",
                    "uid" => "urn:oid:0.9.2342.19200300.100.1.1",
                    "uniqueIdentifier" => "urn:oid:0.9.2342.19200300.100.1.44",
                    "userCertificate" => "urn:oid:2.5.4.36",
                    "userPassword" => "urn:oid:2.5.4.35",
                    "userSMIMECertificate" => "urn:oid:2.16.840.1.113730.3.1.40",
                    "x500uniqueIdentifier" => "urn:oid:2.5.4.45",

                ];

                // Loop through all $_SERVER variables to find Shibboleth attributes
                foreach ($_SERVER as $key => $value) {
                    // Check if the key is a Shibboleth attribute
                    if (strpos($key, 'urn:') === 0 || array_key_exists($key, $attributeUrnMap)) {
                        $urn = $attributeUrnMap[$key] ?? "Not available"; // Get URN or default text
                        echo "<tr><td class=\"text-break\"><strong>{$key}</strong><br><small>{$urn}</small></td><td class=\"cell text-break\">" . htmlspecialchars($value) . "</td></tr>";
                    }
                }
            ?>
        </tbody>
    </table>
</div>

<br>
<div class="w-75 mx-auto">
    <h4 class="garr-text-warning mb-4">Assurance</h4>
    <table class="table table-bordered table-striped" style="table-layout: fixed; width: 100%">
        <thead>
            <tr>
                <th class="garr-bar text-white" style="width: 32%">Name</th>
                <th class="garr-bar text-white" style="width: 17%">Declared</th>
		<th class="garr-bar text-white" style="width: 17%">Authentication</th>
		<th class="garr-bar text-white" style="width: 17%">Defined Values</th>
		<th class="garr-bar text-white" style="width: 17%">Profile Validity</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach ($_SERVER as $key => $value) {
                    if(strpos($key, 'urn:') === 0 || array_key_exists($key, $attributeUrnMap)) {
                        if($key == "eduPersonAssurance"){
                            $metaValues = explode(';', $value);

                        $affiliation_attr = false;

                        foreach ($_SERVER as $second_key => $second_value) {
                            if(strpos($second_key, 'urn:') === 0 || array_key_exists($second_key, $attributeUrnMap)) {
                                if($second_key == "eduPersonAffiliation" || $second_key == "eduPersonScopedAffiliation"){
                                    $affiliation_attr = true;
                                }
                            }
                        }

			            // Loop through all $_SERVER variables to find AuthnContext-Class
                        foreach ($_SERVER as $server_key => $server_value) {
                            if (strpos($server_key, 'Shib-') === 0) {
                                $clean_key2 = substr($server_key, 5);

                                if($clean_key2 == "AuthnContext-Class"){
                                    $authnContext_class = $server_value;

					                // Declaring all possible levels of assurance
                                    $assurance_lvl = ["https://idem.garr.it/af/IDEM-P0","https://idem.garr.it/af/IDEM-P1","https://idem.garr.it/af/IDEM-P2","https://idem.garr.it/af/IDEM-P3"];

                                    $idemp0 = false;
                                    $idemp1 = false;
                                    $idemp2 = false;
                                    $idemp3 = false;

                                    foreach($assurance_lvl as $al){

                                        // Dichiarato
                                        if (in_array($al, $metaValues, true)){
                                            echo "<tr><td class=\"text-break\"><strong>{$al}</strong><td class=\"cell text-break\">True</td>";
                                        } else {
                                        // Non Dichiarato
                                            echo "<tr><td class=\"text-break\"><strong>{$al}</strong><td class=\"cell text-break\">False</td>";
                                        }

                                        // Tipologia Autenticazione
                                        if ($authnContext_class == "urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport" || $authnContext_class == "https://refeds.org/profile/sfa"){
                                            echo "<td class=\"cell text-break\">Single Factor</td>";
                                        } else {
                                            if ($authnContext_class == "https://refeds.org/profile/mfa"){
                                                echo "<td class=\"cell text-break\">Multi Factor</td>";
                                            }
                                        }

				            	        // Checking IDEM-P0 Required Attributes
                                        if($al == "https://idem.garr.it/af/IDEM-P0"){
                                            if (in_array("https://refeds.org/assurance", $metaValues, true) &&
                                                in_array("https://refeds.org/assurance/ID/unique", $metaValues, true) &&
                                                in_array("https://refeds.org/assurance/ID/eppn-unique-no-reassign", $metaValues, true) &&
                                                in_array("https://refeds.org/assurance/IAP/low", $metaValues, true) &&
                                                in_array("https://idem.garr.it/af/IDEM-P0", $metaValues, true)) {
                                                $idemp0 = true;
                                            }

                                            //Attributes Declared
                                            if ($idemp0){
                                                echo "<td class=\"cell text-break\">True</td>";
                                            } else {
                                            //Attributes Not Declared
                                                echo "<td class=\"cell text-break\">False</td>";
                                            }

                                            //Valido
                                            if ($idemp0 && ($authnContext_class == "urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport" || $authnContext_class == "https://refeds.org/profile/sfa" || $authnContext_class == "https://refeds.org/profile/mfa")){
                                                echo "<td class=\"cell text-break\">True</td></tr>";
                                            } else {
                                            //Non Valido
                                                echo "<td class=\"cell text-break\">False</td></tr>";
                                            }
                                        }

					                    // Checking IDEM-P1 Required Attributes
                                        if ($al == "https://idem.garr.it/af/IDEM-P1"){

                                            if($idemp0){
                                                if ($affiliation_attr) {
                                                    if (in_array("https://refeds.org/assurance/IAP/medium", $metaValues, true) &&
                                                        in_array("https://refeds.org/assurance/profile/cappuccino", $metaValues, true) &&
                                                        in_array("https://refeds.org/assurance/ATP/ePA-1m", $metaValues, true) &&
                                                        in_array("https://idem.garr.it/af/IDEM-P1", $metaValues, true)){
                                                            $idemp1 = true;
                                                        }
                                                } else {
                                                    if (in_array("https://refeds.org/assurance/IAP/medium", $metaValues, true) &&
                                                        in_array("https://refeds.org/assurance/profile/cappuccino", $metaValues, true) &&
                                                        in_array("https://idem.garr.it/af/IDEM-P1", $metaValues, true)){
                                                        $idemp1 = true;
                                                    }
                                                }
                                            }

						                    //Attributi Dichiarati
                                            if ($idemp1){
                                                echo "<td class=\"cell text-break\">True</td>";
                                            } else {
                                            //Attributi Non Dichiarati
                                                echo "<td class=\"cell text-break\">False</td>";
                                            }

                                            //Valido
                                            if ($idemp1 && ($authnContext_class == "urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport" || $authnContext_class == "https://refeds.org/profile/sfa" || $authnContext_class == "https://refeds.org/profile/mfa")){
                                                echo "<td class=\"cell text-break\">True</td></tr>";
                                            } else {
                                            //Non Valido
                                                echo "<td class=\"cell text-break\">False</td></tr>";
                                            }
                                        }

					                    // Checking IDEM-P2 Required Attributes
                                        if ($al == "https://idem.garr.it/af/IDEM-P2"){

                                            if($idemp1){
                                                if ($affiliation_attr) {
                                                    if (in_array("https://refeds.org/assurance/IAP/high", $metaValues, true) &&
                                                        in_array("https://refeds.org/assurance/profile/espresso", $metaValues, true) &&
                                                        in_array("https://refeds.org/assurance/ATP/ePA-1d", $metaValues, true) &&
                                                        in_array("https://idem.garr.it/af/IDEM-P2", $metaValues, true)){
                                                        $idemp2 = true;
                                                    }
                                                } else {
                                                    if (in_array("https://refeds.org/assurance/IAP/high", $metaValues, true) &&
                                                        in_array("https://refeds.org/assurance/profile/espresso", $metaValues, true) &&
                                                        in_array("https://idem.garr.it/af/IDEM-P2", $metaValues, true)){
                                                        $idemp2 = true;
                                                    }
                                                }
                                            }

                    						//Attributi Dichiarati
                                            if ($idemp2){
                                                echo "<td class=\"cell text-break\">True</td>";
                                            } else {
                                            //Attributi Non Dichiarati
                                                echo "<td class=\"cell text-break\">False</td>";
                                            }

                                            //Valido
                                            if ($idemp2 && $authnContext_class == "https://refeds.org/profile/mfa"){
                                                echo "<td class=\"cell text-break\">True</td></tr>";
                                            } else {
                                                //Non Valido
                                                echo "<td class=\"cell text-break\">False</td></tr>";
                                            }
                                        }

            				            // Checking IDEM-P3 Required Attributes
                                        if ($al == "https://idem.garr.it/af/IDEM-P3"){
                                            if($idemp2){
                                                if(in_array("https://idem.garr.it/af/IDEM-P3", $metaValues, true)){
                                                    $idemp3 = true;
                                                }
                                            }

                			    			//Attributi Dichiarati
                                            if ($idemp3){
                                                echo "<td class=\"cell text-break\">True</td>";
                                            } else {
                                                //Attributi Non Dichiarati
                                                echo "<td class=\"cell text-break\">False</td>";
                                            }

                                            //Valido
                                            if ($idemp3 && $authnContext_class == "https://refeds.org/profile/mfa"){
                                                echo "<td class=\"cell text-break\">True</td></tr>";
                                            } else {
                                            //Non Valido
                                                echo "<td class=\"cell text-break\">False</td></tr>";
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } ?>
        </tbody>
    </table>
</div>

<div class="w-75 mx-auto">
    <br>
    <h4 class="garr-text-warning mb-4">Session</h4>
    <table class="table table-bordered table-striped" style="table-layout: fixed; width: 100%">
        <thead>
            <tr>
                <th class="garr-bar text-white" style="width: 35%">Name</th>
                <th class="garr-bar text-white" style="width: 65%">Value</th>
            </tr>
        </thead>
        <tbody>
            <?php
                // Loop through all $_SERVER variables to find Shibboleth attributes
                foreach ($_SERVER as $key => $value) {
                    if (strpos($key, 'Shib-') === 0) {
                        $clean_key = substr($key, 5);
                        echo "<tr><td class=\"text-break\"><strong>{$clean_key}</strong></td><td class=\"cell text-break\">" . htmlspecialchars($value) . "</td></tr>";
                    }
                }

                // Loop through all $_SERVER variables to find Shibboleth attributes
                foreach ($_SERVER as $key => $value) {
                    // Check if the key is a Shibboleth attribute
                    if (!empty($value) && !array_key_exists($key, $attributeUrnMap) && !(strpos($key, 'Shib-') === 0 || strpos($key, 'Meta-') === 0)) {
                        echo "<tr><td class=\"text-break\"><strong>{$key}</strong></td><td class=\"cell text-break\">" . htmlspecialchars($value) . "</td></tr>";
                    }
                }
            ?>
        </tbody>
    </table>
    </div>
</div>

<?php include_once '/var/www/html/sp/shared/footer.html'; ?>

</body>
</html>
