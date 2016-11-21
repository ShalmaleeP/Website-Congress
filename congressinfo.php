<!DOCTYPE HTML> 
<html>
<head>
    <title>Forecast</title></head>
    <style type="text/css">
        .fieldset-auto-width{
             display: inline-block;
        }
        body div{
            text-align: center;
        } 
    </style>
    <body>     
        <div>
            <h2> Congress Information Search</h2>
            <fieldset class="fieldset-auto-width">
            <form id="congform" name="cong" method="POST">
                Congress Database
                <select id="pos" name="pos" onchange="writekey()">
                    <option disabled selected>Select your option</option>
                    <option>Legislators</option>
                    <option>Committees</option>
                    <option>Bills</option>
                    <option>Amendments</option>
                </select>

                <br>
                Chamber
                <input id="senate" type="radio" name="chamber" value="senate" checked>Senate
                <input id="house" type="radio" name="chamber" value="house" >House
                <script type="text/javascript">
                    var rad=document.getElementsByName("chamber");
                <?php if(isset($_POST['search']) && isset($_POST['chamber']) && $_POST['chamber'] == 'house'):  ?>    
                    rad[1].checked="true";
                <?php else:  ?>    
                    rad[0].checked="true";
                <?php endif; ?>
                </script>

                <br>
                <input type="hidden" id="keyword" name="keyword" value="Keyword*">
                <label id="keylabel" name="keylabel">Keyword*</label>
                <script type="text/javascript">
                    <?php if(isset($_POST['search'])): ?>
                    document.getElementById('pos').value = "<?php if($_POST['pos']=="") echo "Select your option"; else echo $_POST['pos'];?>";
                    var cdb=document.getElementById('pos').value;
                    var keylab=document.getElementById("keylabel");
                    if(cdb=="Legislators"){
                        keylab.innerHTML="State/ Representative*";
                        //k.value="State/ Representative";   
                    }
                    else if(cdb=="Committees"){
                        //k.value="Committee ID";
                        keylab.innerHTML="Committee ID*";
                    }
                    else if(cdb=="Bills"){
                        //k.value="Bill ID";
                        keylab.innerHTML="Bill ID*";
                    }
                    else if(cdb=="Amendments"){
                        //k.value="Amendment ID";
                        keylab.innerHTML="Amendment ID*";
                    }
                    else
                        keylab.innerHTML="Keyword*";
                    <?php endif; ?>
                </script>

                <input type="text" name="keyval" id="keyval" value=""><br>
                <script type="text/javascript">
                    <?php if(isset($_POST['search'])): ?>
                    document.getElementById('keyval').value = "<?php echo $_POST['keyval'];?>";
                    <?php endif; ?>
                </script>

                <input type="submit" name="search" value="search" onclick="checkempty()">
                <input type="reset" name="clear" value="Clear" onclick="clearfields()">
                
                <br>
                <a href="http://sunlightfoundation.com/" target="_blank">Powered by Sunlight Foundation</a>
            </form>
            </fieldset>
        </div>
        <p style="text-align:center;">
        <?php 
//            if(isset($_POST['clear'])): 
//                        echo "Clear Pressed";
//                       foreach($_POST as $key=>$value){
//                           
//                           echo $key.":".$value."<br>";
//                       } 
//            endif;
            
            
        if(isset($_POST["search"])): {
            if($_POST["pos"]=="Legislators"):{
                $keyvalue=trim($_POST["keyval"]);
                if($_POST["keyval"]!=""):{
                    $flag=0;
                    $newstr="";
                    $cnt=0;
                    
                    $parameters=explode(" ",$keyvalue);
                    //echo "Parameters:".count($parameters);
            
                    if(count($parameters)>1):{
                        for($i=0;$i<count($parameters)-1;$i++){
                            $parameters[$i]=strtolower($parameters[$i]);
                            $parameters[$i]=ucfirst($parameters[$i]); 
                            $newstr=$newstr."".$parameters[$i]."%20";
                        }
                        $newstr=$newstr."".ucfirst(strtolower($parameters[$i]));
                        //echo $newstr;
            
                        $json_string1 = file_get_contents("http://openstates.org/api/v1/metadata/?apikey=2eccdffb3bf4404c8fa4a2eed3b8bba1");
                        $stateval = json_decode($json_string1);

                        foreach($stateval as $item){
                            if(strcasecmp($item->name,$newstr) == 0):
                                $stateab=$item->abbreviation;
                                //$statename=$item->name;
                                $flag=1;
                            endif;
                        }
                        if($flag!=1): {
                            if(count($parameters)==2):
                            $json_string = file_get_contents("http://congress.api.sunlightfoundation.com/legislators?query=".$parameters[0]."&query=".$parameters[1]."&apikey=2eccdffb3bf4404c8fa4a2eed3b8bba1&chamber=".$_POST["chamber"]);
                            endif;
                            if(count($parameters)==3):
                            $json_string = file_get_contents("http://congress.api.sunlightfoundation.com/legislators?query=".$parameters[0]."&query=".$parameters[1]."&query=".$parameters[2]."&apikey=2eccdffb3bf4404c8fa4a2eed3b8bba1&chamber=".$_POST["chamber"]);
                            endif;
                            $flag=2;
                            
                        }
                        endif;
                    }
                    endif;
                

                    $json_string1 = file_get_contents("http://openstates.org/api/v1/metadata/?apikey=2eccdffb3bf4404c8fa4a2eed3b8bba1");
                    $stateval = json_decode($json_string1);
                    
                    foreach($stateval as $item){
                        if(strcasecmp($item->name,$keyvalue) == 0):
                            $stateab=$item->abbreviation;
                            //$statename=$item->name;
                            $flag=1;
                        endif;
                    }
                    
                    if($flag==1):{
                        $stateab=strtoupper($stateab);
                        $json_string = file_get_contents("http://congress.api.sunlightfoundation.com/legislators?apikey=2eccdffb3bf4404c8fa4a2eed3b8bba1&chamber=".$_POST["chamber"]."&state=".$stateab);
                    }
                    endif;
                    if($flag==0):{
                        $json_string = file_get_contents("http://congress.api.sunlightfoundation.com/legislators?apikey=2eccdffb3bf4404c8fa4a2eed3b8bba1&query=".$keyvalue."&chamber=".$_POST["chamber"]);
                    }
                    endif;    
                    $parsed_json = json_decode($json_string);

                    //echo "Total elements=".$parsed_json->count." Per page=".$parsed_json->page->count;
                    if($parsed_json->count!=0):{
                        $totalpages=$parsed_json->count/$parsed_json->page->count;
                        if($parsed_json->count % $parsed_json->page->count!=0):
                            $totalpages=$totalpages+1;
                        endif;
                    }
                    endif;
                    //echo "<br>Total Pages=".$totalpages;
                    if($parsed_json->count==0):
                        echo "The API returned zero results for the request";
                    endif;
                    if($parsed_json->count!=0):{
                        print "<table id='outer' align='center' cellspacing='0'><tr><th style='border:solid 1px;'>Name</th><th style='border:solid 1px;'>State</th><th style='border:solid 1px;'>Chamber</th><th style='border:solid 1px;'>Details</th></tr>";
                        if($flag==1):{
                            for($i=1;$i<=$totalpages;$i++){
                                //echo "by state";
                                $json_string2 = file_get_contents("http://congress.api.sunlightfoundation.com/legislators?apikey=2eccdffb3bf4404c8fa4a2eed3b8bba1&chamber=".$_POST["chamber"]."&state=".$stateab."&page=".$i);
                                $parsed_json2 = json_decode($json_string2);
                                foreach ($parsed_json2->results as $item) {
                                    print "<tr><td style='border:solid 1px; text-align:center;'>".$item->first_name." ".$item->last_name."</td><td style='border:solid 1px; text-align:center;'>".$item->state_name." </td><td style='border:solid 1px; text-align:center;'>".$item->chamber."</td>";
                                    print "<td style='border:solid 1px; text-align:center;'><a href='#' onclick=getdetails(".$item->bioguide_id.")>View Details</a></td>";
                                    
                                    echo "<td><table id='".$item->bioguide_id."' style=' border:solid 1px; display:none; text-align:left;'>";    
                                    echo "<tr><td colspan='2' style='text-align:center;'><img src='https://theunitedstates.io/images/congress/225x275/".$item->bioguide_id.".jpg'></td></tr>";
                                    echo "<tr><td>Full Name</td><td>".$item->title." ".$item->first_name." ".$item->last_name."</td></tr>";
                                    echo "<tr><td>Term Ends On</td><td>".$item->term_end."</td></tr>";
                                    
                                    if(array_key_exists("website",$item)):
                                        echo "<tr><td>Website</td><td><a href='".$item->website."' target='_blank'>".$item->website."</a></td></tr>";
                                    else:
                                        echo "<tr><td>Website</td><td>NA</td></tr>";                                   
                                    endif;
                                    
                                     if(array_key_exists("office",$item)):
                                        echo "<tr><td>Office</td><td>".$item->office."</td></tr>";
                                    else:
                                        echo "<tr><td>Office</td><td>NA</td></tr>";                                 
                                    endif;
                                    
                                    if(array_key_exists("facebook_id",$item)):
                                        echo "<tr><td>Facebook</td><td><a target='_blank' href='https://www.facebook.com/".$item->facebook_id."'>".$item->first_name." ".$item->last_name."</a></td></tr>";                      
                                    else:
                                        echo "<tr><td>Facebook</td><td>NA</td></tr>";                                   
                                    endif;
                                    
                                    if(array_key_exists("twitter_id",$item)):
                                        echo "<tr><td>Twitter</td><td><a target='_blank' href='https://twitter.com/".$item->twitter_id."'>".$item->first_name." ".$item->last_name."</a></td></tr>";
                                    else:
                                        echo "<tr><td>Twitter</td><td>NA</td></tr>";
                                    endif;
                                    echo "</table></td></tr>";
                                }
                            }
                        }
                        else:{
                            //echo "by Name";
                            for($i=1;$i<=$totalpages;$i++){
                                if($flag==0):
                                $json_string2 = file_get_contents("http://congress.api.sunlightfoundation.com/legislators?apikey=2eccdffb3bf4404c8fa4a2eed3b8bba1&chamber=".$_POST["chamber"]."&page=".$i."&query=".$keyvalue);
                                endif;
                                if($flag==2):
                                    if(count($parameters)==2):
                                    $json_string2 = file_get_contents("http://congress.api.sunlightfoundation.com/legislators?query=".$parameters[0]."&query=".$parameters[1]."&apikey=2eccdffb3bf4404c8fa4a2eed3b8bba1&chamber=".$_POST["chamber"]);
                                    endif;
                                    if(count($parameters)==3):
                                    $json_string2 = file_get_contents("http://congress.api.sunlightfoundation.com/legislators?query=".$parameters[0]."&query=".$parameters[1]."&query=".$parameters[2]."&apikey=2eccdffb3bf4404c8fa4a2eed3b8bba1&chamber=".$_POST["chamber"]);
                                    endif;
                                endif;
                                $parsed_json2 = json_decode($json_string2);    
                                foreach ($parsed_json2->results as $item) {
                                    print "<tr><td style='border:solid 1px; text-align:center;'>".$item->first_name." ".$item->last_name."</td><td style='border:solid 1px; text-align:center;'>".$item->state_name." </td><td style='border:solid 1px; text-align:center;'>".$item->chamber."</td>";
                                    print "<td style='border:solid 1px; text-align:center;'><a href='#' onclick=getdetails(".$item->bioguide_id.")>View Details</a></td>";

                                    echo "<td><table id='".$item->bioguide_id."' style=' border:solid 1px; display:none; text-align:left;'>";    
                                    echo "<tr><td colspan='2' style='text-align:center;'><img src='https://theunitedstates.io/images/congress/225x275/".$item->bioguide_id.".jpg'></td></tr>";
                                    echo "<tr><td>Full Name</td><td>".$item->title." ".$item->first_name." ".$item->last_name."</td></tr>";
                                    echo "<tr><td>Term Ends On</td><td>".$item->term_end."</td></tr>";
                                    
                                    if(array_key_exists("website",$item)):
                                        echo "<tr><td>Website</td><td><a href='".$item->website."' target='_blank'>".$item->website."</a></td></tr>";
                                    else:
                                        echo "<tr><td>Website</td><td>NA</td></tr>";                                   
                                    endif;
                                    
                                     if(array_key_exists("office",$item)):
                                        echo "<tr><td>Office</td><td>".$item->office."</td></tr>";
                                    else:
                                        echo "<tr><td>Office</td><td>NA</td></tr>";                                 
                                    endif;
                                    
                                    if(array_key_exists("facebook_id",$item)):
                                        echo "<tr><td>Facebook</td><td><a target='_blank' href='https://www.facebook.com/".$item->facebook_id."'>".$item->first_name." ".$item->last_name."</a></td></tr>";              
                                    else:
                                        echo "<tr><td>Facebook</td><td>NA</td></tr>";                                   
                                    endif;
                                    
                                    if(array_key_exists("twitter_id",$item)):
                                        echo "<tr><td>Twitter</td><td><a target='_blank' href='https://twitter.com/".$item->twitter_id."'>".$item->first_name." ".$item->last_name."</a></td></tr>";
                                    else:
                                        echo "<tr><td>Twitter</td><td>NA</td></tr>";
                                    endif;
                                    
                                    echo "</table></td></tr>";
                                }
                            }
                        }
                        endif;    
                        print "</table>";
                    }
                    endif;
                }
                endif;
            }
            endif;            
            
            
         if($_POST["pos"]=="Committees"):
            $keyvalue=trim($_POST["keyval"]);
            if($_POST["keyval"]!=""):
                //$keyvalue=trim($_POST["keyval"]);
                $keyvalue=strtoupper($keyvalue);
            
                $parameters=explode(" ",$keyvalue);
                //echo "Parameters:".count($parameters);
            
                if(count($parameters)>1):
                    echo "The API returned zero results for the request";
                else:
                    $json_string = file_get_contents("http://congress.api.sunlightfoundation.com/committees?committee_id=".$keyvalue."&chamber=".$_POST["chamber"]."&apikey=2eccdffb3bf4404c8fa4a2eed3b8bba1");
                    $parsed_json = json_decode($json_string); 
                    if($parsed_json->count==0):
                        echo "The API returned zero results for the request";
                    else:
                        print "<table id='outer' align='center' cellspacing='0'><tr><th style='border:solid 1px;'>Committee ID</th><th style='border:solid 1px;'>Committee Name</th><th style='border:solid 1px;'>Chamber</th></tr>";
                        foreach ($parsed_json->results as $item) {
                            print "<tr><td style='border:solid 1px; text-align:center;'>".$item->committee_id."</td>";
                            if(array_key_exists("name",$item)):
                            print "<td style='border:solid 1px; text-align:center;'>".$item->name."</td>";
                            endif;
                            print "<td style='border:solid 1px; text-align:center;'>".$item->chamber."</td>";
                        }
                        print "</table>";
                    endif;
                endif;
            endif;
         endif; 
         
            
         if($_POST["pos"]=="Bills"):
            $keyvalue=trim($_POST["keyval"]);
            if($_POST["keyval"]!=""):
                //$keyvalue=trim($_POST["keyval"]);
                $keyvalue=strtolower($keyvalue);
            
                $parameters=explode(" ",$keyvalue);
                //echo "Parameters:".count($parameters);
            
                if(count($parameters)>1):
                    echo "The API returned zero results for the request";
                else:
                    $json_string = file_get_contents("http://congress.api.sunlightfoundation.com/bills?bill_id=".$keyvalue."&chamber=".$_POST["chamber"]."&apikey=2eccdffb3bf4404c8fa4a2eed3b8bba1");
                    $parsed_json = json_decode($json_string); 
                    if($parsed_json->count==0):
                        echo "The API returned zero results for the request";
                    endif;
                    if($parsed_json->count!=0):
                        $index=1;
                         print "<table id='outer' align='center' cellspacing='0'><tr><th style='border:solid 1px;'>Bill ID</th><th style='border:solid 1px;'>Short Title</th><th style='border:solid 1px;'>Chamber</th><th style='border:solid 1px;'>Details</th></tr>";
                            foreach ($parsed_json->results as $item) {

                                print "<tr><td style='border:solid 1px;text-align:center;'>".$item->bill_id."</td><td style='border:solid 1px; text-align:center;'>".$item->short_title." </td><td style='border:solid 1px; text-align:center;'>".$item->chamber."</td>";
                                print "<td style='border:solid 1px; text-align:center;'><a href='#' onclick=getbilldetails(".$index.")>View Details</a></td>";

                                echo "<td><table id='".$index."' style=' border:solid 1px; display:none; text-align:left;'>";    
                                echo "<tr><td>Bill ID</td><td>".$item->bill_id."</td></tr>";
                                echo "<tr><td>Bill Title</td><td>".$item->short_title."</td></tr>";
                                echo "<tr><td>Sponsor</td><td>".$item->sponsor->title." ".$item->sponsor->first_name." ".$item->sponsor->last_name."</td></tr>";
                                echo "<tr><td>Introduced On</td><td>".$item->introduced_on."</td></tr>";
                                echo "<tr><td>Last Action with date</td><td>".$item->last_version->version_name.", ".$item->last_action_at."</td></tr>";
                                echo "<tr><td>Bill URL</td><td><a target='_blank' href='".$item->last_version->urls->pdf."'>";
                                if($item->short_title!==null): 
                                    echo $item->short_title."</a></td></tr>";
                                else:
                                    echo $item->bill_id."</a></td></tr>";
                                endif;
                                echo "</table></td></tr>";
                                    $index++;
                            }
                        print "</table>";
                    endif;
                endif;
            endif;
        endif;
            
            
        if($_POST["pos"]=="Amendments"):
            $keyvalue=trim($_POST["keyval"]);
            if($_POST["keyval"]!=""):
                //$keyvalue=trim($_POST["keyval"]);
                $parameters=explode(" ",$keyvalue);
                //echo "Parameters:".count($parameters);
            
                if(count($parameters)>1):
                    echo "The API returned zero results for the request";
                else:
                    $keyvalue=strtolower($keyvalue);
                    $json_string = file_get_contents("http://congress.api.sunlightfoundation.com/amendments?amendment_id=".$keyvalue."&chamber=".$_POST["chamber"]."&apikey=2eccdffb3bf4404c8fa4a2eed3b8bba1");
                    $parsed_json = json_decode($json_string); 
                    if($parsed_json->count==0):
                        echo "The API returned zero results for the request";
                    endif;
                    if($parsed_json->count!=0):
                        print "<table id='outer' align='center' cellspacing='0'><tr><th style='border:solid 1px;'>Amendment ID</th><th style='border:solid 1px;'>Amendment Type</th><th style='border:solid 1px;'>Chamber</th><th style='border:solid 1px;'>Inroduced On</th></tr>";
                        foreach ($parsed_json->results as $item) {
                            print "<tr><td style='border:solid 1px; text-align:center;'>".$item->amendment_id."</td><td style='border:solid 1px; text-align:center;'>".$item->amendment_type." </td><td style='border:solid 1px; text-align:center;'>".$item->chamber."</td>";
                            print "<td style='border:solid 1px; text-align:center;'>".$item->introduced_on."</td></tr>";
                        }
                        print "</table>";
                    endif;
                endif;
            endif;
        endif;
            }
            endif;
        
        ?>
        </p>
        
        <script>
            
        function clearfields(){
            var cong=document.getElementById("congform");
            cong.pos.value=cong.pos.options[0];  
            document.getElementById("house").checked = true;
            //var rad=document.getElementsByName("chamber");
            //rad[0].setAttribute("checked","true");
            //rad[1].setAttribute("checked","false");
            var k=document.getElementById("keyword");
            k.value="Keyword*";
            document.getElementById("keylabel").innerHTML="Keyword*";
            document.getElementById("keyval").value="";
            
            var tables = document.getElementsByTagName("table");
            for (var i=tables.length-1; i>=0;i-=1)
                if (tables[i]) tables[i].parentNode.removeChild(tables[i]);
            var para=document.getElementsByTagName("p");
            para[0].innerHTML="";
            
        }
            
            
        function writekey(){
            //var k=document.getElementById("keyword");
            var kl=document.getElementById("keylabel");
            var cong=document.getElementById("congform");
            var congdb=cong.pos.options[cong.pos.selectedIndex].value;
            if(congdb=="Legislators"){
                 kl.innerHTML="State/ Representative*";
                 //k.value="State/ Representative";   
            }
            else if(congdb=="Committees"){
                //k.value="Committee ID";
                kl.innerHTML="Committee ID*";
            }
            else if(congdb=="Bills"){
               // k.value="Bill ID";
                kl.innerHTML="Bill ID*";
            }
            else if(congdb=="Amendments"){
                //k.value="Amendment ID";
                kl.innerHTML="Amendment ID*";
            }
            //document.getElementById("keyval").value="";
        }
            
            
        function checkempty(){
            var pos_filled=false;
            var keyword_filled=false;
            var k=document.getElementById("keyval");
            var txt="Please enter the following missing information: ";
            var cong=document.getElementById("congform");
            if (cong.pos.value != "Select your option") 
                pos_filled=true;
            else {
                txt+="Congress database,";
                //cong.pos.value="Select your option";
            }
            var ab=k.value.trim();
            if (ab != "") 
                keyword_filled=true;
            else txt+="Keyword";
            
            if(txt!="Please enter the following missing information: ")
                alert(txt);
        }
            
        
        function getdetails(item){
            //console.log(item);
            item.style.display="table";
            var tab=document.getElementById('outer');
            var tabrows=tab.rows.length;
            for(i=0;i<tabrows;i++){
                var x = tab.rows[i].cells;
                for(j=0;j<4;j++){
                    x[j].style.display="none";
                }
            }            
        }    
            
            
        function getbilldetails(item){
            //console.log(item);
            document.getElementById(item).style.display="table";
            var tab=document.getElementById('outer');
            var tabrows=tab.rows.length;
            for(i=0;i<tabrows;i++){
                var x = tab.rows[i].cells;
                for(j=0;j<4;j++){
                    x[j].style.display="none";
                }
            }           
        }
    </script>
    </body>
</html>