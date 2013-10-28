<?php
exit;
include 'zzserver.php';
$sql1 = "SELECT a.id, a.bpUserID, a.userID, a.mailSubject, a.mailText, b.id AS intID FROM ".$prefix."bp_mails AS a JOIN ".$prefix."users AS b ON a.userID = b.ids ORDER BY zeit ASC LIMIT 0, 3";
$sql2 = mysql_query($sql1);
while ($sql3 = mysql_fetch_assoc($sql2)) {
    /* prepare the methods parameters */  // DEV/LIVE
    $aRequestParams = array (
        'partnerID'     => 222,
        'projectID'     => 111,
        'bpUserID'      => $sql3['bpUserID'],
        'userID'        => $sql3['intID'],
        'mailSubject'   => $sql3['mailSubject'],
        'mailText'      => $sql3['mailText'],
        'sender'        => 'system@ballmanager.de',
        'authTimestamp' => time()
    );
    /* define output options */
    $aOutputOptions = array (
        'encoding'      => 'utf-8'
    );
    /* generate XML for the request using the request parameters and the output options */
    $sRequestXml = xmlrpc_encode_request("portal.sendUserMail", $aRequestParams, $aOutputOptions);
    $sSecretKey  = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX'; // DEV/LIVE
    /* generate the authHash and append it to the target URL */
    $sAuthHash   = strtolower(md5($sRequestXml . $sSecretKey));
    $sTargetUrl  = 'http://portal-api.bigpoint.net/xmlrpc/?authHash=' . $sAuthHash;
    /* create the stream context that can be sent to the target URL */
    $rContext    = stream_context_create (
        array (
            'http'  => array (
                'method'  => "POST",
                'header'  => "Content-Type: text/xml",
                'content' => $sRequestXml
            )
        )
    );
    /* send the request */
    $sResponseXml  = file_get_contents($sTargetUrl, false, $rContext);
    /* parse the XML response and convert it into a PHP datastructure (array) */
    $aResponse     = xmlrpc_decode($sResponseXml);
    /* handle the response */
    if (xmlrpc_is_fault($aResponse)) {
        trigger_error("xmlrpc:". $aResponse['faultString'] . "(" . $aResponse['faultCode'] . ")");
    } else {
    	$sql4 = "DELETE FROM ".$prefix."bp_mails WHERE id = ".$sql3['id'];
    	$sql5 = mysql_query($sql4);
        print_r($aResponse);
    }
}
?>