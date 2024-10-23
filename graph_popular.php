<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();
date_default_timezone_set('Asia/Manila');
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":


        $sql = "WITH RECURSIVE AllMonths(month_num, month_name) AS (
                SELECT 1, 'January'
                UNION ALL
                SELECT month_num + 1, 
                    CASE month_num + 1
                        WHEN 2 THEN 'Feb'
                        WHEN 3 THEN 'Mar'
                        WHEN 4 THEN 'Apr'
                        WHEN 5 THEN 'May'
                        WHEN 6 THEN 'June'
                        WHEN 7 THEN 'July'
                        WHEN 8 THEN 'Aug'
                        WHEN 9 THEN 'Sept'
                        WHEN 10 THEN 'Oct'
                        WHEN 11 THEN 'Nov'
                        WHEN 12 THEN 'Dec'
                    END
                FROM AllMonths
                WHERE month_num < 12
            ),
            MonthlyData AS (
                SELECT 
                    MONTH(image_uploadDate) AS month_num,
                    fabric,
                    stain,
                    COUNT(*) AS count,
                    ROW_NUMBER() OVER (PARTITION BY MONTH(image_uploadDate) 
                                    ORDER BY COUNT(*) DESC) AS rank_fabric,
                    ROW_NUMBER() OVER (PARTITION BY MONTH(image_uploadDate) 
                                    ORDER BY COUNT(*) DESC) AS rank_stain
                FROM 
                    image
                GROUP BY 
                    MONTH(image_uploadDate), fabric, stain
            ),
            AggregatedData AS (
                SELECT 
                    month_num,
                    MAX(CASE WHEN rank_fabric = 1 THEN fabric END) AS fabric,
                    MAX(CASE WHEN rank_fabric = 1 THEN count END) AS fabricValue,
                    MAX(CASE WHEN rank_stain = 1 THEN stain END) AS stain,
                    MAX(CASE WHEN rank_stain = 1 THEN count END) AS stainValue
                FROM 
                    MonthlyData
                GROUP BY 
                    month_num
            )
            SELECT 
                am.month_name AS month,
                COALESCE(ad.fabric, 'None') AS fabric,
                COALESCE(ad.fabricValue, 0) AS fabricValue,
                COALESCE(ad.stain, 'None') AS stain,
                COALESCE(ad.stainValue, 0) AS stainValue
            FROM 
                AllMonths am
            LEFT JOIN 
                AggregatedData ad ON am.month_num = ad.month_num
            ORDER BY 
                am.month_num;";



        if (isset($sql)) {
            $stmt = $conn->prepare($sql);



            $stmt->execute();
            $student = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($student);
        }


        break;
}
