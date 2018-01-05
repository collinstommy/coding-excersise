<!--
The Irish lottery draw takes place twice weekly on a Wednesday 
and a Saturday at 8pm. Write a function that calculates and returns the next valid
draw date based on the current date and time and also on an optional supplied date.

-->

<html>
 <head>
  <title>PHP Test</title>
 </head>
 
    <body style="font-family: arial"> 
        <p>Please enter date as d-m-y hh:mm format, for example 20-1-2018 17:30.   
        <form action="lottery.php" method="get">
            <input type="text" name="input_date" placeholder="Enter date" />
            <input type="submit" name="submit" />
        </form> 
    <?php 
        if ( isset( $_GET['submit'] ) ) {
            $input = $_REQUEST['input_date'];

            $isValidDate = isValidDateTimeString($input, 'j-n-Y H:i', 'Europe/London');
            if($isValidDate){
                   $drawDate = getDrawDate($input);
                   $dateFormatedInput = date('l jS \of F Y  H:i', strtotime($input));
                    echo "<p><strong>Your entered: </strong>" . $input;
                    echo "<p><strong>Next lottery draw date after " . $dateFormatedInput  . " is : </strong>" . $drawDate  . " at 8pm GMT";
            }
            else{
                 echo "<p><strong>" . $input . " is not a valid date.</strong>";
            }
        }
        else{
            date_default_timezone_set('Europe/London');

            $now = date('j-n-Y H:i');
            $drawDate = getDrawDate($now);

            echo "<p><strong>The current date is: </strong>" . date('l jS \of F Y',  time());
            echo "<p><strong>Next lottery draw date is : </strong>" . $drawDate  . " at 8pm GMT";
        }

        function getDrawDate($inputDate) {
            date_default_timezone_set('Europe/London');

            $dayofweek = date('N',strtotime($inputDate));
            $hour =  date('G',strtotime($inputDate));

            //if its after 8pm on Wednesday and before 5pm on Saturday the next draw is Saturday
            $saturdayDraw =  (($dayofweek == 3 && $hour >= 20) || in_array($dayofweek, array(4,5)) || $dayofweek == 6 && $hour < 20);

            $nextWednesdayDraw = new DateTime($inputDate);
            $nextWednesdayDraw->modify('next wednesday');

            $nextSaturdayDraw = new DateTime($inputDate);
            $nextSaturdayDraw->modify('next saturday');
            
            //if its before 20:00 on a Wednesday on Saturday, today is the draw date
            if($saturdayDraw && $dayofweek == 6 && $hour < 20){
                return date('l jS \of F Y', strtotime($inputDate));
            }
            else if(!$saturdayDraw && $dayofweek == 3 && $hour < 20){
                return date('l jS \of F Y', strtotime($inputDate));
            }
            
            $nextDrawDate =  $saturdayDraw ?  $nextSaturdayDraw : $nextWednesdayDraw;
            return $nextDrawDate->format('l jS \of F Y');
        }

        function isValidDateTimeString($str_dt, $str_dateformat, $str_timezone) {
            //convert input to a datetime and then compare against itself
            //ensures that php does not try to create a datetime from an invalid date
            $date = DateTime::createFromFormat($str_dateformat, $str_dt, new DateTimeZone($str_timezone));
            return $date && $date->format($str_dateformat) == $str_dt;
        }
    ?> 
    </body>
</html>
