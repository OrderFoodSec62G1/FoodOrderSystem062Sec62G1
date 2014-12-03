<?php
include './session.php';
?>
<!-- include header -->
<?php include './operatorHeader.php';?>

<!-- include db connector -->
<?php include '../dbConnector.php';?>


<!-- get parameters and set db table -->
<?php
    $user_input = empty($_POST)?$_GET:$_POST;
    $table = "orders";
    $nPage = $user_input['page'];
    $rdoType = $_GET['type'];
    $chkOption = $_GET['check'];
    $dateon = $_GET['date'];
    $restid = $idRest;
    if($dateon == null)
        $dateon = date("Y-m-d");
?>

<!-- load data from database server -->
<?php
    //select all orders
    $query = "SELECT * FROM $table ";
    
    $query = "SELECT orders.idorder, orders.userid, orders.status, orders.orderTime, orders.username ";
    $query .= "FROM (orderline INNER JOIN orders ON orderline.orderid = orders.idorder) ";
    $query .= "INNER JOIN menu ON orderline.menuid=menu.menuid ";
    $query .= "WHERE menu.restid=$restid AND ";
                
/*
SELECT orders.idorder, orders.userid, orders.status, orders.orderTime, orders.username
FROM 
(orderline INNER JOIN orders
ON orderline.orderid = orders.idorder)
    
INNER JOIN 
menu
ON orderline.menuid=menu.menuid 


WHERE Date(orders.orderTime)=Date("2014-10-05")
GROUP BY orders.idorder
ORDER BY orders.orderTime DESC;
"GROUP BY orders.idorder ORDER BY orders.orderTime DESC;"
*/

    if($rdoType == "all"){
        if($dateon != ""){ 
            $query .= "DATE(orderTime) = DATE('$dateon') ";
        }
    }else{
        if($rdoType == "active"){
            if($chkOption == "all"){
                $query .="(status=0 OR status=1) ";
            }else if($chkOption == "new"){
                $query .="status=0 ";
            }else if($chkOption == "confirmed"){
                $query .="status=1 ";
            }else{
                $query .="status=100 ";
            }
        }else if($rdoType == "inactive"){
            if($chkOption == "all"){
                $query .="(status=2 OR status=3) ";
            }else if($chkOption == "completed"){
                $query .=" status=2";
            }else if($chkOption == "rejected"){
                $query .=" status=3";
            }else{
                $query .=" status=100";
            }
        }else{
            // default status
            $query .=" (status=0 OR status=1)";
            $rdoType ="active";
            $chkOption = "all";
        }
        if($dateon != ""){ 
            $query .= " AND DATE(orderTime) = DATE('$dateon')";
        }
    }
    $query .= " GROUP BY orders.idorder ORDER BY orders.orderTime DESC ";
    
    $result = mysql_query($query); 

    //echo "<br><p>$query</p>";

    //init order data structure
    $order = array(
                    "orderId"=>"",
                    "userId"=>"",
                    "orderTime"=>"",
                    "userName"=>"",
                    "orderStatus"=>""
                );
    //init order list array
    $arrOrder = array();

    // load data to order array
    while ($row = mysql_fetch_array($result)) {
        
        $order["orderId"] = $row["idorder"];
        $order["userId"] = $row["userid"];
        $order["orderTime"] = $row["orderTime"];
        $order["userName"] = $row["username"];
        $order["orderStatus"] = $row["status"];
        array_push($arrOrder, $order);
        //echo "<br><br>";
        //echo"Order Line: {$order['orderId']}, {$order["userId"]}, {$order["orderTime"]} ";
    }
/*
SELECT *
FROM 
(orderline INNER JOIN orders
ON orderline.orderid = orders.idorder)
    
INNER JOIN 
menu
ON orderline.menuid=menu.menuid WHERE menu.restid=1

;
*/


?>


<!-- content begins -->
    <div id="main">
<!-- user search filtering option -->
<?php include './operatorRightMenu.php';?>
        
<!-- data view table -->
<?php include './operatorLeftMenu.php';?>
        
    
    <!--div main ends -->
    </div>

    <!--div back-all ends -->
	</div>

<!--div content ends -->
</div>

<!--footer begins -->

<script type="text/javascript">
        function ChangeColor(tableRow, highLight){
            if (highLight){
                tableRow.style.backgroundColor = '#dcfac9';
            }else{
                tableRow.style.backgroundColor = 'white';
            }
        }

        function DoNav(orderId){
            var url = "./orderdetail.php?orderid=";
            var finalUrl = url.concat(orderId);
            window.location = finalUrl;
        }
        
        function addParameters(url){
            var paramUrl = url.concat("?type=");
            var typeAll = document.getElementById("typeAll").checked;
            var typeActive = document.getElementById('typeActive').checked;
            var typeInactive = document.getElementById('typeInactive').checked
            var dateOn = document.getElementById('dateon').value;
            if(typeAll) {
                //show all radio button is checked
                paramUrl = paramUrl.concat("all");
            }else if(typeActive) {
                //show active radio button is checked
                paramUrl = paramUrl.concat("active");
                var newOrder = document.getElementById('activeNew').checked;
                var confirmed = document.getElementById('activeConfirmed').checked;
                if(newOrder && confirmed) {
                    paramUrl = paramUrl.concat("&check=all");
                }else if (newOrder){
                    paramUrl = paramUrl.concat("&check=new");
                }else if (confirmed){
                    paramUrl = paramUrl.concat("&check=confirmed");
                }
            }else{
                //show inactive radio button is checked
                paramUrl = paramUrl.concat("inactive");
                var completed = document.getElementById('inactiveCompleted').checked;
                var rejected = document.getElementById('inactiveRejected').checked;
                if(completed && rejected) {
                    paramUrl = paramUrl.concat("&check=all");
                }else if (completed){
                    paramUrl = paramUrl.concat("&check=completed");
                }else if (rejected){
                    paramUrl = paramUrl.concat("&check=rejected");
                }
            }
            
            if(dateOn != ""){
                paramUrl = paramUrl.concat("&date=");
                paramUrl = paramUrl.concat(dateOn);
            }
            return paramUrl;
        }
    
        function resetChkBox(){
            var typeAll = document.getElementById("typeAll").checked;
            var typeActive = document.getElementById('typeActive').checked;
            var typeInactive = document.getElementById('typeInactive').checked
            if(typeAll) {
                //show all radio button is checked
                document.getElementById('activeNew').checked = false;
                document.getElementById('activeConfirmed').checked = false;
                document.getElementById('inactiveCompleted').checked = false;
                document.getElementById('inactiveRejected').checked = false;
            }else if(typeActive) {
                //show active radio button is checked
                document.getElementById('activeNew').checked = true;
                document.getElementById('activeConfirmed').checked = true;
                document.getElementById('inactiveCompleted').checked = false;
                document.getElementById('inactiveRejected').checked = false;
            }else{
                //show inactive radio button is checked
                document.getElementById('activeNew').checked = false;
                document.getElementById('activeConfirmed').checked = false;
                document.getElementById('inactiveCompleted').checked = true;
                document.getElementById('inactiveRejected').checked = true;
            }
        }
        
        function reLocate(){
            var paramUrl = addParameters(location.protocol + '//' + location.host + location.pathname);
            //window.alert(paramUrl);
            window.location = paramUrl;
        }
        
        //setTimeout("javascript function", milliseconds);
        setTimeout(reLocate, 10000);
        
    </script> 

<?php include './footer.php';?>


