<?php include('/var/www/html/sp/shared/header.html'); ?>
<?php include('/var/www/html/sp/shared/logout.php'); ?>

<h2 class="garr-text-warning mb-4 text-center">Welcome to SP DEMO Application!</h2>

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
                        echo "<tr><td><strong>{$clean_key}</strong><td class=\"cell\">" . htmlspecialchars($value) . "</td></tr>";
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
                    // Other Common Person Attributes
                    "audio" => "urn:oid:0.9.2342.19200300.100.1.55",
                    "cn" => "urn:oid:2.5.4.3",
                    "description" => "urn:oid:2.5.4.13",
                    "displayName" => "urn:oid:2.16.840.1.113730.3.1.241",
                    "facsimileTelephoneNumber" => "urn:oid:2.5.4.23",
                    "givenName" => "urn:oid:2.5.4.42",
                    "homePhone" => "urn:oid:0.9.2342.19200300.100.1.20",
                    "homePostalAddress" => "urn:oid:0.9.2342.19200300.100.1.39",
                    "initials" => "urn:oid:2.5.4.43",
                    "jpegPhoto" => "urn:oid:0.9.2342.19200300.100.1.60",
                    "l" => "urn:oid:2.5.4.7",
                    "labeledURI" => "urn:oid:1.3.6.1.4.1.250.1.57",
                    "mail" => "urn:oid:0.9.2342.19200300.100.1.3",
                    "manager" => "urn:oid:0.9.2342.19200300.100.1.10",
                    "mobile" => "urn:oid:0.9.2342.19200300.100.1.41",
                    "o" => "urn:oid:2.5.4.10",
                    "ou" => "urn:oid:2.5.4.11",
                    "pager" => "urn:oid:0.9.2342.19200300.100.1.42",
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
                ];

                // Loop through all $_SERVER variables to find Shibboleth attributes
                foreach ($_SERVER as $key => $value) {
                    // Check if the key is a Shibboleth attribute
                    if (strpos($key, 'urn:') === 0 || array_key_exists($key, $attributeUrnMap)) {
                        $urn = $attributeUrnMap[$key] ?? "Not available"; // Get URN or default text
                        echo "<tr><td><strong>{$key}</strong><br><small>{$urn}</small></td><td class=\"cell\">" . htmlspecialchars($value) . "</td></tr>";
                    }
                }
            ?>
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
                        echo "<tr><td><strong>{$clean_key}</strong></td><td class=\"cell\">" . htmlspecialchars($value) . "</td></tr>";
                    }
                }

                // Loop through all $_SERVER variables to find Shibboleth attributes
                foreach ($_SERVER as $key => $value) {
                    // Check if the key is a Shibboleth attribute
                    if (!empty($value) && !array_key_exists($key, $attributeUrnMap) && !(strpos($key, 'Shib-') === 0 || strpos($key, 'Meta-') === 0)) {
                        echo "<tr><td><strong>{$key}</strong></td><td class=\"cell\">" . htmlspecialchars($value) . "</td></tr>";
                    }
                }
            ?>
        </tbody>
    </table>
    </div>
</div>

<?php include('/var/www/html/sp/shared/footer.html'); ?>

</body>
</html>