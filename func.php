<?
	error_reporting(0);
	
	$conn=odbc_connect('dwbi','Atif.Shahzad','shahzad4444');
	if (!$conn)
		exit("Connection Failed: " . $conn);
	

	function startsWith($haystack, $needle)
	{
		return !strncmp($haystack, $needle, strlen($needle));
	}

	function endsWith($haystack, $needle)
	{
		$length = strlen($needle);
		if ($length == 0) return true;
		return (substr($haystack, -$length) === $needle);
	}
	
	

	
	function getRegionByYearById($strType, $nId)
	{
		global $conn;
		$arrRet = array();
		
		switch($strType)
		{
			case "region":
				$strQuery = "select DATEPART(year, dtSaleDate) as rgYear, SUM(fsale) as rgSale from tblsale
						where rgID = $nId
						group by DATEPART(year, dtSaleDate)";
				break;
				
			case "district":

				$strQuery = "select DATEPART(year, dtSaleDate) as rgYear, SUM(fsale) as rgSale from tblsale
						where dstID = $nId
						group by DATEPART(year, dtSaleDate)";			
				break;
				
			case "store":
			
				$strQuery = "select DATEPART(year, dtSaleDate) as rgYear, SUM(fsale) as rgSale from tblsale
						where strID = $nId
						group by DATEPART(year, dtSaleDate)";			
				break;
							
				
			case "employee":			
			
				$strQuery = "select DATEPART(year, dtSaleDate) as rgYear, SUM(fsale) as rgSale from tblsale
						where empID = $nId
						group by DATEPART(year, dtSaleDate)";	
				
				break;
		}
		
		$nResult = odbc_exec($conn, $strQuery);
		
		while (odbc_fetch_row($nResult))
			$arrRet[odbc_result($nResult, "rgYear")] = odbc_result($nResult, "rgSale");
		
		return $arrRet;
	}
	
	function getRegionByYear()
	{
		global $conn;
		
		$strQuery = "SELECT rgID,vrgName,vRegionalManager from tblSaleRegions";
		$nResult = odbc_exec($conn, $strQuery);
				
		while (odbc_fetch_row($nResult))
		{
			$nId = odbc_result($nResult, "rgID");
			$strRM = odbc_result($nResult, "vrgName");					
			$arr = getRegionByYearById("region", $nId);
			$dTotal = number_format($arr['2013'] + $arr['2012'] + $arr['2011'] + $arr['2010'], 2);
					
			echo "<div class='data-row' id='yearly_region_id_$nId'><div>";
			echo "	<div style='float: left; width: 300px;'>$strRM</div>";
			echo "	<div class='number' style='float: left; width: 130px;'>$" . $dTotal . "</div>";	// total
			echo "	<div class='number' style='float: left; width: 130px;'>$" . number_format($arr['2013'], 2) . "</div>";		// 2013
			echo "	<div class='number' style='float: left; width: 130px;'>$" . number_format($arr['2012'], 2) . "</div>";		// 2012
			echo "	<div class='number' style='float: left; width: 130px;'>$" . number_format($arr['2011'], 2) . "</div>";		// 2011
			echo "	<div class='number' style='float: left; width: 130px;'>$" . number_format($arr['2010'], 2) . "</div>";		// 2010
			echo "</div></div>";
		}
	}
	
	function getDistrictsByYear($strId)
	{
		global $conn;
		$id = substr($strId, strlen("yearly_region_id_"));
				
		$strQuery =  "Select  distinct tblSaleDistrictStore.dstID as dstID ,tblSaleDistricts.vDistrictName as dstName
						from tblSaleDistrictStore inner join tblSaleDistricts on 
						tblSaleDistrictStore.dstID = tblSaleDistricts.dstID
						where tblSaleDistrictStore.rgID = $id";
		$nResult = odbc_exec($conn, $strQuery);
		
		while (odbc_fetch_row($nResult))
		{
			$nId = odbc_result($nResult, "dstID");
			$strDistrict = odbc_result($nResult, "dstName");					
			$arr = getRegionByYearById("district", $nId);
			$dTotal = number_format($arr['2013'] + $arr['2012'] + $arr['2011'] + $arr['2010'], 2);
		
			echo "<div class='data-row' id='yearly_district_id_$nId'><div>";
			echo "	<div style='float: left; width: 300px;'>&nbsp;&nbsp;$strDistrict</div>";
			echo "	<div class='number' style='float: left; width: 130px;'>$" . $dTotal . "</div>";	// total
			echo "	<div class='number' style='float: left; width: 130px;'>$" . number_format($arr['2013'], 2) . "</div>";		// 2013
			echo "	<div class='number' style='float: left; width: 130px;'>$" . number_format($arr['2012'], 2) . "</div>";		// 2012
			echo "	<div class='number' style='float: left; width: 130px;'>$" . number_format($arr['2011'], 2) . "</div>";		// 2011
			echo "	<div class='number' style='float: left; width: 130px;'>$" . number_format($arr['2010'], 2) . "</div>";		// 2010
			echo "</div></div>";
		}
	}
	
	function getStoreByYear($strId)
	{
		global $conn;
		$id = substr($strId, strlen("yearly_district_id_"));
		
		$strQuery = "select strID,vStoreName from tblSaleDistrictStore where dstId = $id";
		$nResult = odbc_exec($conn, $strQuery);
		
		while (odbc_fetch_row($nResult))
		{
		
			$nId = odbc_result($nResult, "strID");
			$strStore = odbc_result($nResult, "vStoreName");					
			$arr = getRegionByYearById("store", $nId);
			$dTotal = number_format($arr['2013'] + $arr['2012'] + $arr['2011'] + $arr['2010'], 2);
		
			echo "<div class='data-row' id='yearly_store_id_$nId'><div>";
			echo "	<div style='float: left; width: 300px;'>&nbsp;&nbsp;&nbsp;&nbsp;$strStore</div>";
			echo "	<div class='number' style='float: left; width: 130px;'>$" . $dTotal . "</div>";	// total
			echo "	<div class='number' style='float: left; width: 130px;'>$" . number_format($arr['2013'], 2) . "</div>";		// 2013
			echo "	<div class='number' style='float: left; width: 130px;'>$" . number_format($arr['2012'], 2) . "</div>";		// 2012
			echo "	<div class='number' style='float: left; width: 130px;'>$" . number_format($arr['2011'], 2) . "</div>";		// 2011
			echo "	<div class='number' style='float: left; width: 130px;'>$" . number_format($arr['2010'], 2) . "</div>";		// 2010
			echo "</div></div>";
		
		
		}

	}

	function getEmployeeByYear($strId)
	{
	
		global $conn;
		$id = substr($strId, strlen("yearly_store_id_"));
		
		$strQuery = "select DISTINCT tblSale.empID as empid, Emp.Employee_Name as empname from tblSale 
					inner join ZWirelessRIQ.dbo.iQmetrix_Employees as Emp
					on Emp.Id_Number = tblSale.empID
					where strID = $id";
		$nResult = odbc_exec($conn, $strQuery);
		
		while (odbc_fetch_row($nResult))
		{
		
			$nId = odbc_result($nResult, "empid");
			$strEmployee = odbc_result($nResult, "empname");					
			$arr = getRegionByYearById("employee", $nId);
			$dTotal = number_format($arr['2013'] + $arr['2012'] + $arr['2011'] + $arr['2010'], 2);
		
			echo "<div class='data-row' id='yearly_emp_id_$nId'><div>";
			echo "	<div style='float: left; width: 300px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$strEmployee</div>";
			echo "	<div class='number' style='float: left; width: 130px;'>$" . $dTotal . "</div>";	// total
			echo "	<div class='number' style='float: left; width: 130px;'>$" . number_format($arr['2013'], 2) . "</div>";		// 2013
			echo "	<div class='number' style='float: left; width: 130px;'>$" . number_format($arr['2012'], 2) . "</div>";		// 2012
			echo "	<div class='number' style='float: left; width: 130px;'>$" . number_format($arr['2011'], 2) . "</div>";		// 2011
			echo "	<div class='number' style='float: left; width: 130px;'>$" . number_format($arr['2010'], 2) . "</div>";		// 2010
			echo "</div></div>";
		
		
		}
	
	}

	function getRegionByQuarter()
	{
		global $conn;
		
		$strQuery = "SELECT rgID,vrgName,vRegionalManager from tblSaleRegions";
		$nResult = odbc_exec($conn, $strQuery);
				
		while (odbc_fetch_row($nResult))
		{
			$nId = odbc_result($nResult, "rgID");
			$strRM = odbc_result($nResult, "vrgName");					
			$arr = getRegionByQuarterById("region", $nId);
			$dTotal = number_format($arr['2']['2013'] + $arr['1']['2013'] + $arr['4']['2012'] + $arr['3']['2012']+ $arr['2']['2012']+ $arr['1']['2012'], 2);
				
			echo "<div class='data-row' id='quarterly_region_id_$nId'><div>";
			echo "	<div style='float: left; width: 290px;'>$strRM</div>";
			echo "	<div class='number' style='float: left; width: 100px;'>$" . $dTotal . "</div>";	// total
			echo "	<div class='number' style='float: left; width: 100px;'>$" . number_format($arr['2']['2013'], 2) . "</div>";		// 2Q13
			echo "	<div class='number' style='float: left; width: 100px;'>$" . number_format($arr['1']['2013'], 2) . "</div>";		// 1Q13
			echo "	<div class='number' style='float: left; width: 100px;'>$" . number_format($arr['4']['2012'], 2) . "</div>";		// 4Q12
			echo "	<div class='number' style='float: left; width: 100px;'>$" . number_format($arr['3']['2012'], 2) . "</div>";		// 3Q12
			echo "	<div class='number' style='float: left; width: 100px;'>$" . number_format($arr['2']['2012'], 2) . "</div>";		// 2Q12
			echo "	<div class='number' style='float: left; width: 100px;'>$" . number_format($arr['1']['2012'], 2) . "</div>";		// 1Q12
			echo "</div></div>";
		}
	}
	
	
	function getDistrictsByQuarter($strId)
	{
		global $conn;
		$id = substr($strId, strlen("quarterly_region_id_"));
				
		$strQuery =  "Select  distinct tblSaleDistrictStore.dstID as dstID ,tblSaleDistricts.vDistrictName as dstName
						from tblSaleDistrictStore inner join tblSaleDistricts on 
						tblSaleDistrictStore.dstID = tblSaleDistricts.dstID
						where tblSaleDistrictStore.rgID = $id";
		$nResult = odbc_exec($conn, $strQuery);
		
		while (odbc_fetch_row($nResult))
		{
			$nId = odbc_result($nResult, "dstID");
			$strDistrict = "&nbsp;&nbsp;" . odbc_result($nResult, "dstName");					
			$arr = getRegionByQuarterById("district", $nId);
			$dTotal = number_format($arr['2']['2013'] + $arr['1']['2013'] + $arr['4']['2012'] + $arr['3']['2012']+ $arr['2']['2012']+ $arr['1']['2012'], 2);
				
			echo "<div class='data-row' id='quarterly_district_id_$nId'><div>";
			echo "	<div style='float: left; width: 290px;'>$strDistrict</div>";
			echo "	<div class='number' style='float: left; width: 100px;'>$" . $dTotal . "</div>";	// total
			echo "	<div class='number' style='float: left; width: 100px;'>$" . number_format($arr['2']['2013'], 2) . "</div>";		// 2Q13
			echo "	<div class='number' style='float: left; width: 100px;'>$" . number_format($arr['1']['2013'], 2) . "</div>";		// 1Q13
			echo "	<div class='number' style='float: left; width: 100px;'>$" . number_format($arr['4']['2012'], 2) . "</div>";		// 4Q12
			echo "	<div class='number' style='float: left; width: 100px;'>$" . number_format($arr['3']['2012'], 2) . "</div>";		// 3Q12
			echo "	<div class='number' style='float: left; width: 100px;'>$" . number_format($arr['2']['2012'], 2) . "</div>";		// 2Q12
			echo "	<div class='number' style='float: left; width: 100px;'>$" . number_format($arr['1']['2012'], 2) . "</div>";		// 1Q12
			echo "</div></div>";
		}
	}
	
	function getStoreByQuarter($strId)
	{
		global $conn;
		$id = substr($strId, strlen("quarterly_district_id_"));
		
		$strQuery = "select strID,vStoreName from tblSaleDistrictStore where dstId = $id";
		$nResult = odbc_exec($conn, $strQuery);
		
		while (odbc_fetch_row($nResult))
		{
		
			$nId = odbc_result($nResult, "strID");
			$strStore = "&nbsp;&nbsp;&nbsp;&nbsp;" . odbc_result($nResult, "vStoreName");					
			$arr = getRegionByQuarterById("store", $nId);
			$dTotal = number_format($arr['2']['2013'] + $arr['1']['2013'] + $arr['4']['2012'] + $arr['3']['2012']+ $arr['2']['2012']+ $arr['1']['2012'], 2);
				
			echo "<div class='data-row' id='quarterly_store_id_$nId'><div>";
			echo "	<div style='float: left; width: 290px;'>$strStore</div>";
			echo "	<div class='number' style='float: left; width: 100px;'>$" . $dTotal . "</div>";	// total
			echo "	<div class='number' style='float: left; width: 100px;'>$" . number_format($arr['2']['2013'], 2) . "</div>";		// 2Q13
			echo "	<div class='number' style='float: left; width: 100px;'>$" . number_format($arr['1']['2013'], 2) . "</div>";		// 1Q13
			echo "	<div class='number' style='float: left; width: 100px;'>$" . number_format($arr['4']['2012'], 2) . "</div>";		// 4Q12
			echo "	<div class='number' style='float: left; width: 100px;'>$" . number_format($arr['3']['2012'], 2) . "</div>";		// 3Q12
			echo "	<div class='number' style='float: left; width: 100px;'>$" . number_format($arr['2']['2012'], 2) . "</div>";		// 2Q12
			echo "	<div class='number' style='float: left; width: 100px;'>$" . number_format($arr['1']['2012'], 2) . "</div>";		// 1Q12
			echo "</div></div>";
		
		}

	}

	function getEmployeeByQuarter($strId)
	{
	
		global $conn;
		$id = substr($strId, strlen("quarterly_store_id_"));
		
		$strQuery = "select DISTINCT tblSale.empID as empid, Emp.Employee_Name as empname from tblSale 
					inner join ZWirelessRIQ.dbo.iQmetrix_Employees as Emp
					on Emp.Id_Number = tblSale.empID
					where strID = $id";
		$nResult = odbc_exec($conn, $strQuery);
		
		while (odbc_fetch_row($nResult))
		{
		
			$nId = odbc_result($nResult, "empid");
			$strEmployee = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;	" . odbc_result($nResult, "empname");					
			$arr = getRegionByQuarterById("employee", $nId);
			$dTotal = number_format($arr['2']['2013'] + $arr['1']['2013'] + $arr['4']['2012'] + $arr['3']['2012']+ $arr['2']['2012']+ $arr['1']['2012'], 2);
				
			echo "<div class='data-row' id='quarterly_employee_id_$nId'><div>";
			echo "	<div style='float: left; width: 290px;'>$strEmployee</div>";
			echo "	<div class='number' style='float: left; width: 100px;'>$" . $dTotal . "</div>";	// total
			echo "	<div class='number' style='float: left; width: 100px;'>$" . number_format($arr['2']['2013'], 2) . "</div>";		// 2Q13
			echo "	<div class='number' style='float: left; width: 100px;'>$" . number_format($arr['1']['2013'], 2) . "</div>";		// 1Q13
			echo "	<div class='number' style='float: left; width: 100px;'>$" . number_format($arr['4']['2012'], 2) . "</div>";		// 4Q12
			echo "	<div class='number' style='float: left; width: 100px;'>$" . number_format($arr['3']['2012'], 2) . "</div>";		// 3Q12
			echo "	<div class='number' style='float: left; width: 100px;'>$" . number_format($arr['2']['2012'], 2) . "</div>";		// 2Q12
			echo "	<div class='number' style='float: left; width: 100px;'>$" . number_format($arr['1']['2012'], 2) . "</div>";		// 1Q12
			echo "</div></div>";
		
		
		
		}
	
	}

	function getRegionByMonth()
	{
		global $conn;
		
		$strQuery = "SELECT rgID,vrgName,vRegionalManager from tblSaleRegions";
		$nResult = odbc_exec($conn, $strQuery);
				
		while (odbc_fetch_row($nResult))
		{
			$nId = odbc_result($nResult, "rgID");
			$strRM = odbc_result($nResult, "vrgName");					
			$arr = getRegionByMonthById("region", $nId);
			$dTotal = number_format(($arr['1'] + $arr['2'] + $arr['3'] + $arr['4'] + $arr['5'] + $arr['6'] + $arr['7'] + $arr['8'] + $arr['9'] + $arr['10']+ $arr['11'] + $arr['12'])/1000, 0);
					
			echo "<div class='data-row' id='monthly_region_id_$nId'><div>";
			echo "	<div style='float: left; width: 300px;'>$strRM</div>";
			echo "	<div class='number' style='float: left; width: 70px;'>$" . $dTotal . "k</div>";	// total
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['1']/1000, 0) . "k</div>";		// jan
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['2']/1000, 0) . "k</div>";		// f
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['3']/1000, 0) . "k</div>";		// m
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['4']/1000, 0) . "k</div>";		// a
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['5']/1000, 0) . "k</div>";		// m
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['6']/1000, 0) . "k</div>";		// j
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['7']/1000, 0) . "k</div>";		// j
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['8']/1000, 0) . "k</div>";		// a
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['9']/1000, 0) . "k</div>";		// s
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['10']/1000, 0) . "k</div>";		// o
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['11']/1000, 0) . "k</div>";		// n
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['12']/1000, 0) . "k</div>";		// d
			echo "</div></div>";
		}
	}
	
	function getDistrictsByMonth($strId)
	{
		global $conn;
		$id = substr($strId, strlen("monthly_region_id_"));
			
		$strQuery =  "Select  distinct tblSaleDistrictStore.dstID as dstID ,tblSaleDistricts.vDistrictName as dstName
						from tblSaleDistrictStore inner join tblSaleDistricts on 
						tblSaleDistrictStore.dstID = tblSaleDistricts.dstID
						where tblSaleDistrictStore.rgID = $id";
		$nResult = odbc_exec($conn, $strQuery);
		
		while (odbc_fetch_row($nResult))
		{
			$nId = odbc_result($nResult, "dstID");
			$strDistrict = odbc_result($nResult, "dstName");					
			$arr = getRegionByMonthById("district", $nId);
			$dTotal = number_format(($arr['1'] + $arr['2'] + $arr['3'] + $arr['4'] + $arr['5'] + $arr['6'] + $arr['7'] + $arr['8'] + $arr['9'] + $arr['10']+ $arr['11'] + $arr['12'])/1000, 0);
					
			echo "<div class='data-row' id='monthly_district_id_$nId'><div>";
			echo "	<div style='float: left; width: 300px;'>&nbsp;&nbsp;$strDistrict</div>";
			echo "	<div class='number' style='float: left; width: 70px;'>$" . $dTotal . "</div>";	// total
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['1']/1000, 0) . "k</div>";		// jan
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['2']/1000, 0) . "k</div>";		// f
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['3']/1000, 0) . "k</div>";		// m
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['4']/1000, 0) . "k</div>";		// a
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['5']/1000, 0) . "k</div>";		// m
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['6']/1000, 0) . "k</div>";		// j
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['7']/1000, 0) . "k</div>";		// j
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['8']/1000, 0) . "k</div>";		// a
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['9']/1000, 0) . "k</div>";		// s
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['10']/1000, 0) . "k</div>";		// o
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['11']/1000, 0) . "k</div>";		// n
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['12']/1000, 0) . "k</div>";		// d

			echo "</div></div>";
		}
	}
	function getStoreByMonth($strId)
	{
		global $conn;
		$id = substr($strId, strlen("monthly_district_id_"));
		
		$strQuery = "select strID,vStoreName from tblSaleDistrictStore where dstId = $id";
		$nResult = odbc_exec($conn, $strQuery);
		
		while (odbc_fetch_row($nResult))
		{
		
			$nId = odbc_result($nResult, "strID");
			$strStore = odbc_result($nResult, "vStoreName");					
			$arr = getRegionByMonthById("store", $nId);
			$dTotal = number_format(($arr['1'] + $arr['2'] + $arr['3'] + $arr['4'] + $arr['5'] + $arr['6'] + $arr['7'] + $arr['8'] + $arr['9'] + $arr['10']+ $arr['11'] + $arr['12'])/1000, 0);
					
			echo "<div class='data-row' id='monthly_store_id_$nId'><div>";
			echo "	<div style='float: left; width: 300px;'>&nbsp;&nbsp;&nbsp;&nbsp;$strStore</div>";
			echo "	<div class='number' style='float: left; width: 70px;'>$" . $dTotal . "k</div>";	// total
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['1']/1000, 0) . "k</div>";		// jan
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['2']/1000, 0) . "k</div>";		// f
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['3']/1000, 0) . "k</div>";		// m
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['4']/1000, 0) . "k</div>";		// a
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['5']/1000, 0) . "k</div>";		// m
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['6']/1000, 0) . "k</div>";		// j
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['7']/1000, 0) . "k</div>";		// j
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['8']/1000, 0) . "k</div>";		// a
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['9']/1000, 0) . "k</div>";		// s
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['10']/1000, 0) . "k</div>";		// o
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['11']/1000, 0) . "k</div>";		// n
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['12']/1000, 0) . "k</div>";		// d
			echo "</div></div>";
		
		}

	}

	function getEmployeeByMonth($strId)
	{
	
		global $conn;
		$id = substr($strId, strlen("monthly_store_id_"));
		
		$strQuery = "select DISTINCT tblSale.empID as empid, Emp.Employee_Name as empname from tblSale 
					inner join ZWirelessRIQ.dbo.iQmetrix_Employees as Emp
					on Emp.Id_Number = tblSale.empID
					where strID = $id";
		$nResult = odbc_exec($conn, $strQuery);
		
		while (odbc_fetch_row($nResult))
		{	
			$nId = odbc_result($nResult, "empid");
			$strEmployee = odbc_result($nResult, "empname");					
			$arr = getRegionByMonthById("employee", $nId);
			$dTotal = number_format(($arr['1'] + $arr['2'] + $arr['3'] + $arr['4'] + $arr['5'] + $arr['6'] + $arr['7'] + $arr['8'] + $arr['9'] + $arr['10']+ $arr['11'] + $arr['12'])/1000, 0);
					
			echo "<div class='data-row' id='monthly_employee_id_$nId'><div>";
			echo "	<div style='float: left; width: 300px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$strEmployee</div>";
			echo "	<div class='number' style='float: left; width: 70px;'>$" . $dTotal . "k</div>";	// total
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['1']/1000, 0) . "k</div>";		// jan
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['2']/1000, 0) . "k</div>";		// f
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['3']/1000, 0) . "k</div>";		// m
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['4']/1000, 0) . "k</div>";		// a
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['5']/1000, 0) . "k</div>";		// m
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['6']/1000, 0) . "k</div>";		// j
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['7']/1000, 0) . "k</div>";		// j
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['8']/1000, 0) . "k</div>";		// a
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['9']/1000, 0) . "k</div>";		// s
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['10']/1000, 0) . "k</div>";		// o
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['11']/1000, 0) . "k</div>";		// n
			echo "	<div class='number' style='float: left; width: 50px;'>$" . number_format($arr['12']/1000, 0) . "k</div>";		// d
			echo "</div></div>";
		
		}
	
	}

function getRegionByQuarterById($strType, $nId)
	{
		global $conn;
		$arrRet = array();
		
		switch($strType)
		{
			case "region":
				$strQuery = "select DATEPART(quarter, dtSaleDate) as rgQ,  DATEPART(year, dtSaleDate) as rgYr,SUM(fsale) as rgSale 
								from tblsale
								where rgID = $nId
								group by DATEPART(QUARTER, dtSaleDate),  DATEPART(year, dtSaleDate)";
				break;
				
			case "district":

				$strQuery = "select DATEPART(quarter, dtSaleDate) as rgQ,  DATEPART(year, dtSaleDate) as rgYr,SUM(fsale) as rgSale 
								from tblsale
								where dstID = $nId
								group by DATEPART(QUARTER, dtSaleDate),  DATEPART(year, dtSaleDate)";
				break;
				
			case "store":
			
				$strQuery = "select DATEPART(quarter, dtSaleDate) as rgQ,  DATEPART(year, dtSaleDate) as rgYr,SUM(fsale) as rgSale 
								from tblsale
								where strID = $nId
								group by DATEPART(QUARTER, dtSaleDate),  DATEPART(year, dtSaleDate)";			
				break;
							
				
			case "employee":			
			
				$strQuery = "select DATEPART(quarter, dtSaleDate) as rgQ,  DATEPART(year, dtSaleDate) as rgYr,SUM(fsale) as rgSale 
								from tblsale
								where empID = $nId
								group by DATEPART(QUARTER, dtSaleDate),  DATEPART(year, dtSaleDate)";
				
				break;
		}
		
		$nResult = odbc_exec($conn, $strQuery);
		
		while (odbc_fetch_row($nResult))
			$arrRet[odbc_result($nResult, "rgQ")][odbc_result($nResult, "rgYr")] = odbc_result($nResult, "rgSale");
		
		return $arrRet;
	}
	

	function getRegionByMonthById($strType, $nId)
	{
		global $conn;
		$arrRet = array();
		
		switch($strType)
		{
			case "region":
				$strQuery = "select DATEPART(month, dtSaleDate) as rgm,SUM(fsale) as rgSale 
								from tblsale
								where rgID = $nId and DATEPART(year, dtSaleDate) = 2013
								group by DATEPART(month, dtSaleDate)";
				break;
				
			case "district":

				$strQuery = "select DATEPART(month, dtSaleDate) as rgm,SUM(fsale) as rgSale 
								from tblsale
								where dstID = $nId and DATEPART(year, dtSaleDate) = 2013
								group by DATEPART(month, dtSaleDate)";
				break;
				
			case "store":
			
				$strQuery = "select DATEPART(month, dtSaleDate) as rgm,SUM(fsale) as rgSale 
								from tblsale
								where strID = $nId and DATEPART(year, dtSaleDate) = 2013
								group by DATEPART(month, dtSaleDate)";			
				break;
							
				
			case "employee":			
			
				$strQuery = "select DATEPART(month, dtSaleDate) as rgm,SUM(fsale) as rgSale 
								from tblsale
								where empID = $nId and DATEPART(year, dtSaleDate) = 2013
								group by DATEPART(month, dtSaleDate)";
				
				break;
		}
		//$nResult ="";
		$nResult = odbc_exec($conn, $strQuery);
		
		while (odbc_fetch_row($nResult))
			$arrRet[odbc_result($nResult, "rgm")] = odbc_result($nResult, "rgSale");
		
		return $arrRet;
	}
	
function getSalesWeeklyComparison(){
			global $conn;
		$arrRet = array();
			$strQuery = "select DATEPART(WEEK, dtSaleDate) as week,round(SUM(fsale),2) as rgSale , DATEPART(year, dtSaleDate) as yr
								from tblsale
								where 								
								 DATEPART(year, dtSaleDate) IN (2012,2013,2011,2010)
								group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
								order by week";
	//	echo $conn;
								
		$nResult = odbc_exec($conn, $strQuery);
		
		while (odbc_fetch_row($nResult))
				//echo odbc_result($nResult, "week");
					 $arrRet[odbc_result($nResult, "week")][odbc_result($nResult, "yr")]= odbc_result($nResult, "rgSale");
		
	///	return array(1,2,4);
		return $arrRet;

}

function getSalesWOW($strType, $nId,$tabular = "",$stdate="",$enddate="")
	{
		global $conn;
		$arrRet = array();
		
		switch($strType)
		{						
				
				case "company":				
			  $strQuery = "select * from(select TOP 15 DATEPART(WEEK, dtSaleDate) as week,IsNull(round(SUM(fsale),2),0) as rgSale , DATEPART(year, dtSaleDate) as yr
								from tblsale
			where
				dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";			
				
				break;
	
				
			case "region":
				$strQuery = "select * from(
			select TOP 15 DATEPART(WEEK, dtSaleDate) as week,IsNull(round(SUM(fsale),2),0) as rgSale , DATEPART(year, dtSaleDate) as yr
								from tblsale
								where	
								rgID= $nId AND																					
								dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";
				break;
					
			case "district":
								
				$strQuery ="select * from(select TOP 15 DATEPART(WEEK, dtSaleDate) as week,IsNull(round(SUM(fsale),2),0) as rgSale , DATEPART(year, dtSaleDate) as yr
								from tblsale
								where	
								dstID= $nId AND													
								dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";
				break;
				
			case "store":			
											
				$strQuery = "select * from(select TOP 15 DATEPART(WEEK, dtSaleDate) as week,IsNull(round(SUM(fsale),2),0) as rgSale , DATEPART(year, dtSaleDate) as yr
								from tblsale
								where	
								strID = $nId AND													
								dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";			
				break;
							
				
			case "employee":			
			
												
				$strQuery = "select * from(select TOP 15 DATEPART(WEEK, dtSaleDate) as week,IsNull(round(SUM(fsale),2),0) as rgSale , DATEPART(year, dtSaleDate) as yr
								from tblsale
								where	
								empID = $nId AND														
								dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";			
				
				break;
		}
		$weeks_array 				= array();	
		$yearPrevious 				= explode('-',$stdate);
		$yearCurrent 				= explode('-',$enddate);																											
		$previouseYearWeekEndNumber = "";
		$currentYearStartWeekNumber = "";	
		$week_counter 				= 0;
		$arrs_Ret 					= array();
	    $startWeekNumber			= getWeekNumber($stdate);
	    $endWeekNumber				= getWeekNumber($enddate);	
		 if($yearPrevious[0]!=$yearCurrent[0])
		 {
							 #echo "Insert";	
			$previousYearEndDate		= $yearPrevious[0]."-31-12";//Year-Day-Month
			$previouseYearWeekEndNumber = getWeekNumber($previousYearEndDate);
			$currentYearStartDate		= $yearCurrent[0]."-01-01";//Year Day Month
			$currentYearStartWeekNumber = getWeekNumber($currentYearStartDate);
						 	
	  }	
		   $generatedWeeks 				= generateWeeks($startWeekNumber,$endWeekNumber,$yearPrevious[0],$yearCurrent[0],$previouseYearWeekEndNumber,$currentYearStartWeekNumber);
		
		$nResult = odbc_exec($conn, $strQuery);
		
		while (odbc_fetch_row($nResult))		
		{			$weeks_array[]= odbc_result($nResult, "week");
					$year_value = substr(odbc_result($nResult, "yr"),2);				
					$arrRet[odbc_result($nResult, "week")."w".$year_value]["Sales"]= odbc_result($nResult, "rgSale");
					
		}
		
		foreach($generatedWeeks as $generateYearKey=>$generateVal)
	    {
			if(!in_array($generateVal,$weeks_array))
			{
				$arrRet[$generateYearKey]["Sales"]	= 0;
			}
	   }
	   
	   foreach($generatedWeeks as $generateYearKey=>$generateVal)
	   {
		   $arrs_Ret[$generateYearKey]["Sales"]		= $arrRet[$generateYearKey]["Sales"];
	  			 
	   }
	   #print_r($arrs_Ret);
		return $arrs_Ret;
		#return $arrRet;
		
	
	}
	function getSalesMOM($strType, $nId)
	{
		global $conn;
		$arrRet = array();
		
		switch($strType)
		{
		
						
			 case "company":
			 $strQuery = "select DATEPART(MONTH, dtSaleDate) as month,round(SUM(fsale),2) as rgSale , DATEPART(year, dtSaleDate) as yr
								from tblsale	
								WHERE												
								DATEPART(year, dtSaleDate) IN (2012,2013,2011,2010)
								group by DATEPART(Month, dtSaleDate), DATEPART(year, dtSaleDate)
								order by month";
				break;
				
		
			case "region":
				 $strQuery = "select DATEPART(MONTH, dtSaleDate) as month,round(SUM(fsale),2) as rgSale , DATEPART(year, dtSaleDate) as yr
								from tblsale
								where 	
								rgID= $nId AND							
								DATEPART(year, dtSaleDate) IN (2012,2013,2011,2010)
								group by DATEPART(Month, dtSaleDate), DATEPART(year, dtSaleDate)
								order by month";
				break;
				
			case "district":
								
				$strQuery = "select DATEPART(MONTH, dtSaleDate) as month,round(SUM(fsale),2) as rgSale , DATEPART(year, dtSaleDate) as yr
								from tblsale
								where 	
								dstID= $nId AND							
								DATEPART(year, dtSaleDate) IN (2012,2013,2011,2010)
								group by DATEPART(MONTH, dtSaleDate), DATEPART(year, dtSaleDate)
								order by month";
				break;
				
			case "store":			
											
				$strQuery = "select DATEPART(MONTH, dtSaleDate) as month,round(SUM(fsale),2) as rgSale , DATEPART(year, dtSaleDate) as yr
								from tblsale
								where 	
								strID = $nId AND							
								DATEPART(year, dtSaleDate) IN (2012,2013,2011,2010)
								group by DATEPART(MONTH, dtSaleDate), DATEPART(year, dtSaleDate)
								order by month";			
				break;
							
				
			case "employee":			
			
												
				$strQuery = "select DATEPART(MONTH, dtSaleDate) as month,round(SUM(fsale),2) as rgSale , DATEPART(year, dtSaleDate) as yr
								from tblsale
								where 	
								empID = $nId AND							
								DATEPART(year, dtSaleDate) IN (2012,2013,2011,2010)
								group by DATEPART(MONTH, dtSaleDate), DATEPART(year, dtSaleDate)
								order by month";			
				
				break;
		}
		
		$nResult = odbc_exec($conn, $strQuery);
		while (odbc_fetch_row($nResult))				
					 $arrRet[odbc_result($nResult, "month")][odbc_result($nResult, "yr")]= odbc_result($nResult, "rgSale");
		
	
		return $arrRet;
		
	
	}

	
	function getSalesYOY($strType, $nId)
	{
		global $conn;
		$arrRet = array();
		
		switch($strType)
		{
			case "company":
				$strQuery = "select round(SUM(fsale),2) as rgSale , DATEPART(year, dtSaleDate) as yr
								from tblsale
								where 								
								DATEPART(year, dtSaleDate) IN (2012,2013,2011,2010)
								group by DATEPART(year, dtSaleDate)
								order by yr";
				break;
			case "region":
				$strQuery = "select round(SUM(fsale),2) as rgSale , DATEPART(year, dtSaleDate) as yr
								from tblsale
								where 	
								rgID= $nId AND							
								DATEPART(year, dtSaleDate) IN (2012,2013,2011,2010)
								group by DATEPART(year, dtSaleDate)
								order by yr";
				break;
				
			case "district":
								
				$strQuery = "select round(SUM(fsale),2) as rgSale , DATEPART(year, dtSaleDate) as yr
								from tblsale
								where 	
								dstID= $nId AND							
								DATEPART(year, dtSaleDate) IN (2012,2013,2011,2010)
								group by DATEPART(year, dtSaleDate)
								order by yr";
				break;
				
			case "store":			
											
				$strQuery = "select round(SUM(fsale),2) as rgSale , DATEPART(year, dtSaleDate) as yr
								from tblsale
								where 	
								strID = $nId AND							
								DATEPART(year, dtSaleDate) IN (2012,2013,2011,2010)
								group by  DATEPART(year, dtSaleDate)
								order by yr";			
				break;
							
				
			case "employee":			
			
												
				$strQuery = "select round(SUM(fsale),2) as rgSale , DATEPART(year, dtSaleDate) as yr
								from tblsale
								where 	
								empID = $nId AND							
								DATEPART(year, dtSaleDate) IN (2012,2013,2011,2010)
								group by DATEPART(year, dtSaleDate)
								order by yr";			
				
				break;
		}
		
		$nResult = odbc_exec($conn, $strQuery);
		while (odbc_fetch_row($nResult))				
					 $arrRet[odbc_result($nResult, "yr")]= odbc_result($nResult, "rgSale");
		
	
		return $arrRet;
		
	
	}

	function getRegions()
	{
		global $conn;
		$arrRet = array();
		//$strQuery = "SELECT rgID,vrgName,vRegionalManager from tblSaleRegions";
		$strQuery = "SELECT rgID,vrgName+ ' ('+vRegionalManager+')' as vrgName from tblSaleRegions";
		$nResult = odbc_exec($conn, $strQuery);
		
		while (odbc_fetch_row($nResult))
				$arrRet[odbc_result($nResult, "rgID")]= odbc_result($nResult, "vrgName");
		

		return $arrRet;

	}
	
	
	function getDistrictsByRegion($strId)
	{
		global $conn;
		$id = $strId;
			
		$strQuery =  "Select  distinct tblSaleDistrictStore.dstID as dstID ,(tblSaleDistricts.vDistrictName+ ' ('+tblSaleDistricts.vDistrictManagerName)+')' as dstName
						from tblSaleDistrictStore inner join tblSaleDistricts on 
						tblSaleDistrictStore.dstID = tblSaleDistricts.dstID
						where tblSaleDistrictStore.rgID = $id";
		$nResult = odbc_exec($conn, $strQuery);
		while (odbc_fetch_row($nResult))
				$arrRet[odbc_result($nResult, "dstID")]= odbc_result($nResult, "dstName");
		

		return $arrRet;
	}
	
	function getStoreByDistricts($strId)
	{
		global $conn;
		$id =$strId;
		
		$strQuery = "select strID,vStoreName from tblSaleDistrictStore where dstId = $id";
		$nResult = odbc_exec($conn, $strQuery);
		while (odbc_fetch_row($nResult))
				$arrRet[odbc_result($nResult, "strID")]= odbc_result($nResult, "vStoreName");
		

		return $arrRet;
	}
	
	function getEmployeeByStore($strId)
	{
	
		global $conn;
		$id =$strId;
		
		$strQuery = "select DISTINCT tblSale.empID as empid, Emp.Employee_Name as empname from tblSale 
					inner join ZWirelessRIQ.dbo.iQmetrix_Employees as Emp
					on Emp.Id_Number = tblSale.empID
					where strID = $id";
		$nResult = odbc_exec($conn, $strQuery);
		while (odbc_fetch_row($nResult))
				$arrRet[odbc_result($nResult, "empid")]= odbc_result($nResult, "empname");
		

		return $arrRet;
	}
	
		function getProfitWOWProfitPerBox($strType, $nId,$tabular = "",$stdate,$enddate)
	{
		global $conn;
		$arrRet = array();
		
		switch($strType)
		{
		
		case "company":
			$strQuery= "select * from(
			select TOP 15 DATEPART(WEEK, dtSaleDate) as week,
			round(SUM(fPhoneSale),2) as rgphoneSale , 
			round(SUM(fPhoneProfit),2) as rgphoneprofit , 
			SUM(iPhoneQty) as rgphoneqty,
			round(SUM(fServicesSale),2) as rgservicesSale , 
			round(SUM(fServicesProfit),2) as rgservicesprofit , 
			SUM(iServicesQty) as rgservicesqty,
			round(SUM(fInsuranceSale),2) as rginsuranceSale , 
			round(SUM(fInsuranceProfit),2) as rginsuranceprofit , 
			SUM(iInsuranceQty) as rginsuranceqty,
			round(SUM(fAccessorySale),2) as rgaccessorySale , 
			round(SUM(fAccessoryProfit),2) as rgaccessoryprofit , 
			SUM(iAccessoryQty) as rgaccessoryqty,
			ISNULL(round(SUM(fPhoneSale),2),0)+ISNULL(round(SUM(fServicesSale),2),0)+ISNULL(round(SUM(fInsuranceSale),2),0)+ISNULL(round(SUM(fAccessorySale),2),0)  as totalsale,
			ISNULL(round(SUM(fPhoneProfit),2),0)+ISNULL(round(SUM(fServicesProfit),2),0)+ISNULL(round(SUM(fInsuranceProfit),2),0)+ISNULL(round(SUM(fAccessoryProfit),2),0) as totalprofit,
			DATEPART(year, dtSaleDate) as yr
			from tblsale
			where 				
			dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";
				break;
			
		
		
		
			case "region":
			$strQuery= "select * from(
			select TOP 15 DATEPART(WEEK, dtSaleDate) as week,
			round(SUM(fPhoneSale),2) as rgphoneSale , 
			round(SUM(fPhoneProfit),2) as rgphoneprofit , 
			SUM(iPhoneQty) as rgphoneqty,
			round(SUM(fServicesSale),2) as rgservicesSale , 
			round(SUM(fServicesProfit),2) as rgservicesprofit , 
			SUM(iServicesQty) as rgservicesqty,
			round(SUM(fInsuranceSale),2) as rginsuranceSale , 
			round(SUM(fInsuranceProfit),2) as rginsuranceprofit , 
			SUM(iInsuranceQty) as rginsuranceqty,
			round(SUM(fAccessorySale),2) as rgaccessorySale , 
			round(SUM(fAccessoryProfit),2) as rgaccessoryprofit , 
			SUM(iAccessoryQty) as rgaccessoryqty,
			ISNULL(round(SUM(fPhoneSale),2),0)+ISNULL(round(SUM(fServicesSale),2),0)+ISNULL(round(SUM(fInsuranceSale),2),0)+ISNULL(round(SUM(fAccessorySale),2),0)  as totalsale,
			ISNULL(round(SUM(fPhoneProfit),2),0)+ISNULL(round(SUM(fServicesProfit),2),0)+ISNULL(round(SUM(fInsuranceProfit),2),0)+ISNULL(round(SUM(fAccessoryProfit),2),0) as totalprofit,
			DATEPART(year, dtSaleDate) as yr
			from tblsale
			where 	
			rgID = $nId AND							
			dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";
				break;
				
			case "district":
								
				$strQuery= "select * from(
			select TOP 15 DATEPART(WEEK, dtSaleDate) as week,
					round(SUM(fPhoneSale),2) as rgphoneSale , 
					round(SUM(fPhoneProfit),2) as rgphoneprofit , 
					SUM(iPhoneQty) as rgphoneqty,
					round(SUM(fServicesSale),2) as rgservicesSale , 
					round(SUM(fServicesProfit),2) as rgservicesprofit , 
					SUM(iServicesQty) as rgservicesqty,
					round(SUM(fInsuranceSale),2) as rginsuranceSale , 
					round(SUM(fInsuranceProfit),2) as rginsuranceprofit , 
					SUM(iInsuranceQty) as rginsuranceqty,
					round(SUM(fAccessorySale),2) as rgaccessorySale , 
					round(SUM(fAccessoryProfit),2) as rgaccessoryprofit , 
					SUM(iAccessoryQty) as rgaccessoryqty,
					ISNULL(round(SUM(fPhoneSale),2),0)+ISNULL(round(SUM(fServicesSale),2),0)+ISNULL(round(SUM(fInsuranceSale),2),0)+ISNULL(round(SUM(fAccessorySale),2),0)  as totalsale,
					ISNULL(round(SUM(fPhoneProfit),2),0)+ISNULL(round(SUM(fServicesProfit),2),0)+ISNULL(round(SUM(fInsuranceProfit),2),0)+ISNULL(round(SUM(fAccessoryProfit),2),0) as totalprofit,
					DATEPART(year, dtSaleDate) as yr
					from tblsale
					where 	
					dstID = $nId AND							
					dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";
				break;
				
			case "store":			
											
	$strQuery= "select * from(
			select TOP 15 DATEPART(WEEK, dtSaleDate) as week,
					round(SUM(fPhoneSale),2) as rgphoneSale , 
					round(SUM(fPhoneProfit),2) as rgphoneprofit , 
					SUM(iPhoneQty) as rgphoneqty,
					round(SUM(fServicesSale),2) as rgservicesSale , 
					round(SUM(fServicesProfit),2) as rgservicesprofit , 
					SUM(iServicesQty) as rgservicesqty,
					round(SUM(fInsuranceSale),2) as rginsuranceSale , 
					round(SUM(fInsuranceProfit),2) as rginsuranceprofit , 
					SUM(iInsuranceQty) as rginsuranceqty,
					round(SUM(fAccessorySale),2) as rgaccessorySale , 
					round(SUM(fAccessoryProfit),2) as rgaccessoryprofit , 
					SUM(iAccessoryQty) as rgaccessoryqty,
					ISNULL(round(SUM(fPhoneSale),2),0)+ISNULL(round(SUM(fServicesSale),2),0)+ISNULL(round(SUM(fInsuranceSale),2),0)+ISNULL(round(SUM(fAccessorySale),2),0)  as totalsale,
					ISNULL(round(SUM(fPhoneProfit),2),0)+ISNULL(round(SUM(fServicesProfit),2),0)+ISNULL(round(SUM(fInsuranceProfit),2),0)+ISNULL(round(SUM(fAccessoryProfit),2),0) as totalprofit,
					DATEPART(year, dtSaleDate) as yr
					from tblsale
					where 	
					strID = $nId AND							
					dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";			
				break;
							
				
			case "employee":			
			
												
	$strQuery= "select * from(
			select TOP 15 DATEPART(WEEK, dtSaleDate) as week,
					round(SUM(fPhoneSale),2) as rgphoneSale , 
					round(SUM(fPhoneProfit),2) as rgphoneprofit , 
					SUM(iPhoneQty) as rgphoneqty,
					round(SUM(fServicesSale),2) as rgservicesSale , 
					round(SUM(fServicesProfit),2) as rgservicesprofit , 
					SUM(iServicesQty) as rgservicesqty,
					round(SUM(fInsuranceSale),2) as rginsuranceSale , 
					round(SUM(fInsuranceProfit),2) as rginsuranceprofit , 
					SUM(iInsuranceQty) as rginsuranceqty,
					round(SUM(fAccessorySale),2) as rgaccessorySale , 
					round(SUM(fAccessoryProfit),2) as rgaccessoryprofit , 
					SUM(iAccessoryQty) as rgaccessoryqty,
					ISNULL(round(SUM(fPhoneSale),2),0)+ISNULL(round(SUM(fServicesSale),2),0)+ISNULL(round(SUM(fInsuranceSale),2),0)+ISNULL(round(SUM(fAccessorySale),2),0)  as totalsale,
					ISNULL(round(SUM(fPhoneProfit),2),0)+ISNULL(round(SUM(fServicesProfit),2),0)+ISNULL(round(SUM(fInsuranceProfit),2),0)+ISNULL(round(SUM(fAccessoryProfit),2),0) as totalprofit,
					DATEPART(year, dtSaleDate) as yr
					from tblsale
					where 	
					empID = $nId AND							
					dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";		
				
				break;
		}
		$weeks_array 				= array();	
		$yearPrevious 				= explode('-',$stdate);
		$yearCurrent 				= explode('-',$enddate);																											
		$previouseYearWeekEndNumber = "";
		$currentYearStartWeekNumber = "";	
		$week_counter 				= 0;
		$arrs_Ret 					= array();
	    $startWeekNumber			= getWeekNumber($stdate);
	    $endWeekNumber				= getWeekNumber($enddate);	
		 if($yearPrevious[0]!=$yearCurrent[0])
		 {
							 #echo "Insert";	
			$previousYearEndDate		= $yearPrevious[0]."-31-12";//Year-Day-Month
			$previouseYearWeekEndNumber = getWeekNumber($previousYearEndDate);
			$currentYearStartDate		= $yearCurrent[0]."-01-01";//Year Day Month
			$currentYearStartWeekNumber = getWeekNumber($currentYearStartDate);
						 	
	  }	
		   $generatedWeeks 				= generateWeeks($startWeekNumber,$endWeekNumber,$yearPrevious[0],$yearCurrent[0],$previouseYearWeekEndNumber,$currentYearStartWeekNumber);
		$nResult = odbc_exec($conn, $strQuery);
		        if($tabular == ""){
					
							
								
						while (odbc_fetch_row($nResult)){
								
						//$week[odbc_result($nResult, "week")] = true ;
						$weeks_array[]= odbc_result($nResult, "week");
						$year_value = substr(odbc_result($nResult, "yr"),2);
						$rgphoneqty        = odbc_result($nResult, "rgphoneqty");
						$rgphoneprofit     = odbc_result($nResult, "rgphoneprofit");
						$rgservicesprofit  = odbc_result($nResult, "rgservicesprofit");
						$rginsuranceprofit = odbc_result($nResult, "rginsuranceprofit");
						$rgaccessoryprofit = odbc_result($nResult, "rgaccessoryprofit");
						 #$year_value = odbc_result($nResult, "yr");
						if(isset($rgphoneqty) && round($rgphoneqty)!=0 && $rgphoneqty!="" && isset($rgphoneprofit) && round($rgphoneprofit)!=0 && $rgphoneprofit!=""){
						$arrRet[odbc_result($nResult, "week")."w".$year_value]["PPB_Phone"] =  round(($rgphoneprofit/$rgphoneqty));
						}else{
						$arrRet[odbc_result($nResult, "week")."w".$year_value]["PPB_Phone"]  = 0;
						}
						if(isset($rgphoneqty) && round($rgphoneqty)!=0 && $rgphoneqty!="" && isset($rgservicesprofit) && round($rgservicesprofit)!=0 && $rgservicesprofit!=""){
							$arrRet[odbc_result($nResult, "week")."w".$year_value]["PPB_Services"] =  round(($rgservicesprofit/$rgphoneqty));
						}else{
							$arrRet[odbc_result($nResult, "week")."w".$year_value]["PPB_Services"]  = 0;
						}
					if(isset($rgphoneqty) && round($rgphoneqty)!=0 && $rgphoneqty!="" && isset($rginsuranceprofit) && round($rginsuranceprofit)!=0 && $rginsuranceprofit!=""){
						$arrRet[odbc_result($nResult, "week")."w".$year_value]["PPB_Insurance"] =  round(($rginsuranceprofit/$rgphoneqty));
					}else{
						$arrRet[odbc_result($nResult, "week")."w".$year_value]["PPB_Insurance"]  = 0;
					}
							
					if(isset($rgphoneqty) && round($rgphoneqty)!=0 && $rgphoneqty!="" && isset($rgaccessoryprofit) && round($rgaccessoryprofit)!=0 && $rgaccessoryprofit!=""){
						$arrRet[odbc_result($nResult, "week")."w".$year_value]["PPB_Accessories"] =  round(($rgaccessoryprofit/$rgphoneqty));
					}else{
						$arrRet[odbc_result($nResult, "week")."w".$year_value]["PPB_Accessories"]  = 0;
					}
					
			 
		  }	//end while 
		  foreach($generatedWeeks as $generateYearKey=>$generateVal)
			   	   {
	   						if(!in_array($generateVal,$weeks_array))
							{
								$arrRet[$generateYearKey]["PPB_Phone"]        = 0;
								$arrRet[$generateYearKey]["PPB_Services"]     = 0;
								$arrRet[$generateYearKey]["PPB_Insurance"]    = 0;
								$arrRet[$generateYearKey]["PPB_Accessories"]  = 0;
								
							
							}
				  }//Assign values
				   foreach($generatedWeeks as $generateYearKey=>$generateVal)
			  	  {
					  		$arrs_Ret[$generateYearKey]["PPB_Phone"]        = $arrRet[$generateYearKey]["PPB_Phone"];
							$arrs_Ret[$generateYearKey]["PPB_Services"]     = $arrRet[$generateYearKey]["PPB_Services"];
							$arrs_Ret[$generateYearKey]["PPB_Insurance"]    = $arrRet[$generateYearKey]["PPB_Insurance"];
							$arrs_Ret[$generateYearKey]["PPB_Accessories"]  = $arrRet[$generateYearKey]["PPB_Accessories"];
				  }
			 
	   }else{
					
			        while (odbc_fetch_row($nResult)){
			        
			      					$weeks_array[]= odbc_result($nResult, "week");
						 			$year_value = substr(odbc_result($nResult, "yr"),2);
									$rgphoneqty        = odbc_result($nResult, "rgphoneqty");
									$rgphoneprofit     = odbc_result($nResult, "rgphoneprofit");
									$rgservicesprofit  = odbc_result($nResult, "rgservicesprofit");
									$rginsuranceprofit = odbc_result($nResult, "rginsuranceprofit");
									$rgaccessoryprofit = odbc_result($nResult, "rgaccessoryprofit");
									$arrRet[odbc_result($nResult, "week")."w".$year_value]["totalPhoneSold"] 			= $rgphoneqty;
									$arrRet[odbc_result($nResult, "week")."w".$year_value]["phoneProfit"]				= $rgphoneprofit;
						
									
									if(isset($rgphoneqty) && round($rgphoneqty)!=0 && $rgphoneqty!="" && isset($rgphoneprofit) && round($rgphoneprofit)!=0 && $rgphoneprofit!=""){
									$arrRet[odbc_result($nResult, "week")."w".$year_value]["PPB_phone"] =  round(($rgphoneprofit/$rgphoneqty));
									}else{
									$arrRet[odbc_result($nResult, "week")."w".$year_value]["PPB_phone"]  = 0;
									}
									$arrRet[odbc_result($nResult, "week")."w".$year_value]["servicesProfit"]			= $rgservicesprofit;
									if(isset($rgphoneqty) && round($rgphoneqty)!=0 && $rgphoneqty!="" && isset($rgservicesprofit) && round($rgservicesprofit)!=0 && $rgservicesprofit!=""){
									$arrRet[odbc_result($nResult, "week")."w".$year_value]["PPB_services"] =  round(($rgservicesprofit/$rgphoneqty));
									}else{
									$arrRet[odbc_result($nResult, "week")."w".$year_value]["PPB_services"]  = 0;
									}
									$arrRet[odbc_result($nResult, "week")."w".$year_value]["insuranceProfit"]			= $rginsuranceprofit;
									if(isset($rgphoneqty) && round($rgphoneqty)!=0 && $rgphoneqty!="" && isset($rginsuranceprofit) && round($rginsuranceprofit)!=0 && $rginsuranceprofit!=""){
									$arrRet[odbc_result($nResult, "week")."w".$year_value]["PPB_insurance"] =  round(($rginsuranceprofit/$rgphoneqty));
									}else{
									$arrRet[odbc_result($nResult, "week")."w".$year_value]["PPB_insurance"]  = 0;
									}
									$arrRet[odbc_result($nResult, "week")."w".$year_value]["accessoryProfit"]		    = $rgaccessoryprofit;
						
						
									if(isset($rgphoneqty) && round($rgphoneqty)!=0 && $rgphoneqty!="" && isset($rgaccessoryprofit) && round($rgaccessoryprofit)!=0 && $rgaccessoryprofit!=""){
										$arrRet[odbc_result($nResult, "week")."w".$year_value]["PPB_accessory"] =  round(($rgaccessoryprofit/$rgphoneqty));
									}else{
										$arrRet[odbc_result($nResult, "week")."w".$year_value]["PPB_accessory"]  = 0;
									}
		         		}//end while
						
						 foreach($generatedWeeks as $generateYearKey=>$generateVal)
			   	         {
	   						if(!in_array($generateVal,$weeks_array))
							{
								$arrRet[$generateYearKey]["totalPhoneSold"] 			= 0;
								$arrRet[$generateYearKey]["phoneProfit"]				= 0;
								$arrRet[$generateYearKey]["PPB_phone"]  				= 0;
								$arrRet[$generateYearKey]["servicesProfit"]				= 0;
								$arrRet[$generateYearKey]["PPB_services"]  				= 0;
								$arrRet[$generateYearKey]["insuranceProfit"]			= 0;
								$arrRet[$generateYearKey]["PPB_insurance"] 				= 0;
								$arrRet[$generateYearKey]["accessoryProfit"]		    = 0;
								$arrRet[$generateYearKey]["PPB_accessory"]  			= 0;
							}
				  }
				  foreach($generatedWeeks as $generateYearKey=>$generateVal)
			  	  {
					  			$arrs_Ret[$generateYearKey]["totalPhoneSold"] 			= $arrRet[$generateYearKey]["totalPhoneSold"];
								$arrs_Ret[$generateYearKey]["phoneProfit"]				= $arrRet[$generateYearKey]["phoneProfit"];
								$arrs_Ret[$generateYearKey]["PPB_phone"]  				= $arrRet[$generateYearKey]["PPB_phone"];
								$arrs_Ret[$generateYearKey]["servicesProfit"]			= $arrRet[$generateYearKey]["servicesProfit"];
								$arrs_Ret[$generateYearKey]["PPB_services"]  			= $arrRet[$generateYearKey]["PPB_services"];
								$arrs_Ret[$generateYearKey]["insuranceProfit"]			= $arrRet[$generateYearKey]["insuranceProfit"];
								$arrs_Ret[$generateYearKey]["PPB_insurance"] 			= $arrRet[$generateYearKey]["PPB_insurance"];
								$arrs_Ret[$generateYearKey]["accessoryProfit"]		    = $arrRet[$generateYearKey]["accessoryProfit"];
								$arrs_Ret[$generateYearKey]["PPB_accessory"]  			= $arrRet[$generateYearKey]["PPB_accessory"];
					  
				  }
				  #print_r($arrs_Ret);
				
				}//end  else
				# print_r($arrs_Ret);
				 return $arrs_Ret;	
				 #return $arrRet;
}
	function getGrossSalesWOW($strType, $nId,$tabular = "",$stdate,$enddate)
	{
		#echo   $stdate."-----".$enddate."---------------";
		
		
		/*if($year1[0]!=$year2[0]){
//			$orderby = " order by week desc, yr desc";
			$orderby = " order by week";
		}else{
		$orderby = " order by week";
		}*/
		
		global $conn;
		$arrRet = array();
		
		switch($strType)
		{
		
		case "company":
			$strQuery= "select * from(
			select TOP 15 DATEPART(WEEK, dtSaleDate) as week,
			round(SUM(fPhoneSale),2) as rgphoneSale , 
			round(SUM(fPhoneProfit),2) as rgphoneprofit , 
			SUM(iPhoneQty) as rgphoneqty,
			round(SUM(fServicesSale),2) as rgservicesSale , 
			round(SUM(fServicesProfit),2) as rgservicesprofit , 
			SUM(iServicesQty) as rgservicesqty,
			round(SUM(fInsuranceSale),2) as rginsuranceSale , 
			round(SUM(fInsuranceProfit),2) as rginsuranceprofit , 
			SUM(iInsuranceQty) as rginsuranceqty,
			round(SUM(fAccessorySale),2) as rgaccessorySale , 
			round(SUM(fAccessoryProfit),2) as rgaccessoryprofit , 
			SUM(iAccessoryQty) as rgaccessoryqty,
			ISNULL(round(SUM(fPhoneSale),2),0)+ISNULL(round(SUM(fServicesSale),2),0)+ISNULL(round(SUM(fInsuranceSale),2),0)+ISNULL(round(SUM(fAccessorySale),2),0)  as totalsale,
			ISNULL(round(SUM(fPhoneProfit),2),0)+ISNULL(round(SUM(fServicesProfit),2),0)+ISNULL(round(SUM(fInsuranceProfit),2),0)+ISNULL(round(SUM(fAccessoryProfit),2),0) as totalprofit,
			DATEPART(year, dtSaleDate) as yr
			from tblsale
			where
				dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc
			";
			
			#echo $strQuery;
				break;
			
		
		
		
			case "region":
			$strQuery= "select * from(select TOP 15 DATEPART(WEEK, dtSaleDate) as week,
			round(SUM(fPhoneSale),2) as rgphoneSale , 
			round(SUM(fPhoneProfit),2) as rgphoneprofit , 
			SUM(iPhoneQty) as rgphoneqty,
			round(SUM(fServicesSale),2) as rgservicesSale , 
			round(SUM(fServicesProfit),2) as rgservicesprofit , 
			SUM(iServicesQty) as rgservicesqty,
			round(SUM(fInsuranceSale),2) as rginsuranceSale , 
			round(SUM(fInsuranceProfit),2) as rginsuranceprofit , 
			SUM(iInsuranceQty) as rginsuranceqty,
			round(SUM(fAccessorySale),2) as rgaccessorySale , 
			round(SUM(fAccessoryProfit),2) as rgaccessoryprofit , 
			SUM(iAccessoryQty) as rgaccessoryqty,
			ISNULL(round(SUM(fPhoneSale),2),0)+ISNULL(round(SUM(fServicesSale),2),0)+ISNULL(round(SUM(fInsuranceSale),2),0)+ISNULL(round(SUM(fAccessorySale),2),0)  as totalsale,
			ISNULL(round(SUM(fPhoneProfit),2),0)+ISNULL(round(SUM(fServicesProfit),2),0)+ISNULL(round(SUM(fInsuranceProfit),2),0)+ISNULL(round(SUM(fAccessoryProfit),2),0) as totalprofit,
			DATEPART(year, dtSaleDate) as yr
			from tblsale
			where 	
			rgID = $nId AND							
			dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";
				break;
				
			case "district":
								
	 $strQuery= "select * from(select TOP 15 DATEPART(WEEK, dtSaleDate) as week,
					round(SUM(fPhoneSale),2) as rgphoneSale , 
					round(SUM(fPhoneProfit),2) as rgphoneprofit , 
					SUM(iPhoneQty) as rgphoneqty,
					round(SUM(fServicesSale),2) as rgservicesSale , 
					round(SUM(fServicesProfit),2) as rgservicesprofit , 
					SUM(iServicesQty) as rgservicesqty,
					round(SUM(fInsuranceSale),2) as rginsuranceSale , 
					round(SUM(fInsuranceProfit),2) as rginsuranceprofit , 
					SUM(iInsuranceQty) as rginsuranceqty,
					round(SUM(fAccessorySale),2) as rgaccessorySale , 
					round(SUM(fAccessoryProfit),2) as rgaccessoryprofit , 
					SUM(iAccessoryQty) as rgaccessoryqty,
					ISNULL(round(SUM(fPhoneSale),2),0)+ISNULL(round(SUM(fServicesSale),2),0)+ISNULL(round(SUM(fInsuranceSale),2),0)+ISNULL(round(SUM(fAccessorySale),2),0)  as totalsale,
					ISNULL(round(SUM(fPhoneProfit),2),0)+ISNULL(round(SUM(fServicesProfit),2),0)+ISNULL(round(SUM(fInsuranceProfit),2),0)+ISNULL(round(SUM(fAccessoryProfit),2),0) as totalprofit,
					DATEPART(year, dtSaleDate) as yr
					from tblsale
					where 	
					dstID = $nId AND							
					dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";
				break;
				
			case "store":			
											
	$strQuery= "select * from(select TOP 15 DATEPART(WEEK, dtSaleDate) as week,
					round(SUM(fPhoneSale),2) as rgphoneSale , 
					round(SUM(fPhoneProfit),2) as rgphoneprofit , 
					SUM(iPhoneQty) as rgphoneqty,
					round(SUM(fServicesSale),2) as rgservicesSale , 
					round(SUM(fServicesProfit),2) as rgservicesprofit , 
					SUM(iServicesQty) as rgservicesqty,
					round(SUM(fInsuranceSale),2) as rginsuranceSale , 
					round(SUM(fInsuranceProfit),2) as rginsuranceprofit , 
					SUM(iInsuranceQty) as rginsuranceqty,
					round(SUM(fAccessorySale),2) as rgaccessorySale , 
					round(SUM(fAccessoryProfit),2) as rgaccessoryprofit , 
					SUM(iAccessoryQty) as rgaccessoryqty,
					ISNULL(round(SUM(fPhoneSale),2),0)+ISNULL(round(SUM(fServicesSale),2),0)+ISNULL(round(SUM(fInsuranceSale),2),0)+ISNULL(round(SUM(fAccessorySale),2),0)  as totalsale,
					ISNULL(round(SUM(fPhoneProfit),2),0)+ISNULL(round(SUM(fServicesProfit),2),0)+ISNULL(round(SUM(fInsuranceProfit),2),0)+ISNULL(round(SUM(fAccessoryProfit),2),0) as totalprofit,
					DATEPART(year, dtSaleDate) as yr
					from tblsale
					where 	
					strID = $nId AND							
					dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";			
				break;
							
				
			case "employee":			
			
												
	    $strQuery= "select * from(select TOP 15 DATEPART(WEEK, dtSaleDate) as week,
					round(SUM(fPhoneSale),2) as rgphoneSale , 
					round(SUM(fPhoneProfit),2) as rgphoneprofit , 
					SUM(iPhoneQty) as rgphoneqty,
					round(SUM(fServicesSale),2) as rgservicesSale , 
					round(SUM(fServicesProfit),2) as rgservicesprofit , 
					SUM(iServicesQty) as rgservicesqty,
					round(SUM(fInsuranceSale),2) as rginsuranceSale , 
					round(SUM(fInsuranceProfit),2) as rginsuranceprofit , 
					SUM(iInsuranceQty) as rginsuranceqty,
					round(SUM(fAccessorySale),2) as rgaccessorySale , 
					round(SUM(fAccessoryProfit),2) as rgaccessoryprofit , 
					SUM(iAccessoryQty) as rgaccessoryqty,
					ISNULL(round(SUM(fPhoneSale),2),0)+ISNULL(round(SUM(fServicesSale),2),0)+ISNULL(round(SUM(fInsuranceSale),2),0)+ISNULL(round(SUM(fAccessorySale),2),0)  as totalsale,
					ISNULL(round(SUM(fPhoneProfit),2),0)+ISNULL(round(SUM(fServicesProfit),2),0)+ISNULL(round(SUM(fInsuranceProfit),2),0)+ISNULL(round(SUM(fAccessoryProfit),2),0) as totalprofit,
					DATEPART(year, dtSaleDate) as yr
					from tblsale
					where 	
					empID = $nId AND							
					dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";		
				
				break;
		}
							
																
															
		$weeks_array 				= array();	
		$yearPrevious 				= explode('-',$stdate);
		$yearCurrent 				= explode('-',$enddate);																											
		$previouseYearWeekEndNumber = "";
		$currentYearStartWeekNumber = "";	
		$week_counter 				= 0;
		$arrs_Ret 					= array();
	    $startWeekNumber			= getWeekNumber($stdate);
	    $endWeekNumber				= getWeekNumber($enddate);	
		 if($yearPrevious[0]!=$yearCurrent[0])
		 {
							 #echo "Insert";	
			$previousYearEndDate		= $yearPrevious[0]."-31-12";//Year-Day-Month
			$previouseYearWeekEndNumber = getWeekNumber($previousYearEndDate);
			$currentYearStartDate		= $yearCurrent[0]."-01-01";//Year Day Month
			$currentYearStartWeekNumber = getWeekNumber($currentYearStartDate);
						 	
	  }	
		   $generatedWeeks 				= generateWeeks($startWeekNumber,$endWeekNumber,$yearPrevious[0],$yearCurrent[0],$previouseYearWeekEndNumber,$currentYearStartWeekNumber);						
		
		$nResult = odbc_exec($conn, $strQuery);
		        if($tabular == "")
				{
			       while (odbc_fetch_row($nResult)){
					    
				   		$weeks_array[]= odbc_result($nResult, "week");
						$year_value = substr(odbc_result($nResult, "yr"),2);
						 $arrRet[odbc_result($nResult, "week")."w".$year_value]["Sales"]				= odbc_result($nResult, "totalsale");
						 $arrRet[odbc_result($nResult, "week")."w".$year_value]["Profit"]				= odbc_result($nResult, "totalprofit");
				   }//end while
				   foreach($generatedWeeks as $generateYearKey=>$generateVal)
			   	   {
	   						if(!in_array($generateVal,$weeks_array))
							{
								$arrRet[$generateYearKey]["Sales"]			= 0;
					 		    $arrRet[$generateYearKey]["Profit"]		    = 0;
							
							}
				  }
				  foreach($generatedWeeks as $generateYearKey=>$generateVal)
			  	  {
				  
			        $arrs_Ret[$generateYearKey]["Sales"]	=$arrRet[$generateYearKey]["Sales"];
					$arrs_Ret[$generateYearKey]["Profit"]	=$arrRet[$generateYearKey]["Profit"];
				 }
				}else{
						
						//GENERATE WEEEKS
						
						
						 
						
						 
						# echo "--";
						 #print_r($generatedWeeks);
						#echo "--";
						 
						/* while (odbc_fetch_row($nResult)){
							$weeks_array[]= odbc_result($nResult, "week");
							
						 }
						 echo count($weeks_array);
							print_r($weeks_array);*/
							//GENERATE WEEKS
							
							while (odbc_fetch_row($nResult)){
									$weeks_array[]= odbc_result($nResult, "week");
									$year_value = substr(odbc_result($nResult, "yr"),2);
						 			$arrRet[odbc_result($nResult, "week")."w".$year_value]["PhoneSale"]				= odbc_result($nResult, "rgphoneSale");
					 				$arrRet[odbc_result($nResult, "week")."w".$year_value]["PhoneProfit"]				= odbc_result($nResult, "rgphoneprofit");
					 				$arrRet[odbc_result($nResult, "week")."w".$year_value]["PhoneQuantity"]			= odbc_result($nResult, "rgphoneqty");
					 
					 
									 $arrRet[odbc_result($nResult, "week")."w".$year_value]["ServicesSale"]				= odbc_result($nResult, "rgservicesSale");
									 $arrRet[odbc_result($nResult, "week")."w".$year_value]["ServicesProfit"]			= odbc_result($nResult, "rgservicesprofit");
									 $arrRet[odbc_result($nResult, "week")."w".$year_value]["ServicesQuantity"]			= odbc_result($nResult, "rgservicesqty");
					 
					 
					 
									 $arrRet[odbc_result($nResult, "week")."w".$year_value]["InsuranceSale"]		    = odbc_result($nResult, "rginsuranceSale");
									 $arrRet[odbc_result($nResult, "week")."w".$year_value]["InsuranceProfit"]			= odbc_result($nResult, "rginsuranceprofit");
					 				 $arrRet[odbc_result($nResult, "week")."w".$year_value]["InsuranceQuantity"]		= odbc_result($nResult, "rginsuranceqty");
					 
					 
									$arrRet[odbc_result($nResult, "week")."w".$year_value]["AccessoriesSale"]		    = odbc_result($nResult, "rgaccessorySale");
									$arrRet[odbc_result($nResult, "week")."w".$year_value]["AccessoriesProfit"]		    = odbc_result($nResult, "rgaccessoryprofit");
									$arrRet[odbc_result($nResult, "week")."w".$year_value]["AccessoriesQuantity"]		    = odbc_result($nResult, "rgaccessoryqty");
			        
		       }//end while
			  
			   foreach($generatedWeeks as $generateYearKey=>$generateVal)
			   {
	   						if(!in_array($generateVal,$weeks_array))
							{
								    #$nowYear = substr($generateYearKey,2);
						 			$arrRet[$generateYearKey]["PhoneSale"]				 = 0;
					 				$arrRet[$generateYearKey]["PhoneProfit"]		     = 0;
					 				$arrRet[$generateYearKey]["PhoneQuantity"]			 = 0;
					 
									$arrRet[$generateYearKey]["ServicesSale"]			= 0;
									$arrRet[$generateYearKey]["ServicesProfit"]			= 0;
									$arrRet[$generateYearKey]["ServicesQuantity"]		= 0;
					 
									$arrRet[$generateYearKey]["InsuranceSale"]		    = 0;
									$arrRet[$generateYearKey]["InsuranceProfit"]		= 0;
					 				$arrRet[$generateYearKey]["InsuranceQuantity"]		= 0;
					 
									$arrRet[$generateYearKey]["AccessoriesSale"]		= 0;
									$arrRet[$generateYearKey]["AccessoriesProfit"]		= 0;
									$arrRet[$generateYearKey]["AccessoriesQuantity"]	= 0;
								
							}
						}
						 foreach($generatedWeeks as $generateYearKey=>$generateVal)
			  			 {
						   			$arrs_Ret[$generateYearKey]["PhoneSale"]	=$arrRet[$generateYearKey]["PhoneSale"];
					 				$arrs_Ret[$generateYearKey]["PhoneProfit"]=$arrRet[$generateYearKey]["PhoneProfit"];
					 				$arrs_Ret[$generateYearKey]["PhoneQuantity"]=$arrRet[$generateYearKey]["PhoneQuantity"];
					 
									$arrs_Ret[$generateYearKey]["ServicesSale"]=$arrRet[$generateYearKey]["ServicesSale"];
									$arrs_Ret[$generateYearKey]["ServicesProfit"]=$arrRet[$generateYearKey]["ServicesProfit"];			
									$arrs_Ret[$generateYearKey]["ServicesQuantity"]=$arrRet[$generateYearKey]["ServicesQuantity"];
					 
									$arrs_Ret[$generateYearKey]["InsuranceSale"]=$arrRet[$generateYearKey]["InsuranceSale"];
									$arrs_Ret[$generateYearKey]["InsuranceProfit"]=$arrRet[$generateYearKey]["InsuranceProfit"];
					 				$arrs_Ret[$generateYearKey]["InsuranceQuantity"]=$arrRet[$generateYearKey]["InsuranceQuantity"];
					 
									$arrs_Ret[$generateYearKey]["AccessoriesSale"]=$arrRet[$generateYearKey]["AccessoriesSale"];
									$arrs_Ret[$generateYearKey]["AccessoriesProfit"]=$arrRet[$generateYearKey]["AccessoriesProfit"];
									$arrs_Ret[$generateYearKey]["AccessoriesQuantity"]=$arrRet[$generateYearKey]["AccessoriesQuantity"];
						 }
			
	   								#return $arrs_Ret;
						#ksort($arrRet,SORT_NUMERIC);	
						$startOfWeek = getDateFromWeekYear(53,2013,365);
						echo "dateOfweek".$startOfWeek;
				}//end else

				
				 return $arrs_Ret;
}

function getGpByCategorySalesWOW($strType, $nId,$tabular = "",$stdate,$enddate)
	{
		
	global $conn;
		$arrRet = array();
		
		switch($strType)
		{
		
		case "company":
  $strQuery= "select * from(
			select TOP 15 DATEPART(WEEK, dtSaleDate) as week,
			round(SUM(fPhoneSale),2) as rgphoneSale , 
			round(SUM(fPhoneProfit),2) as rgphoneprofit , 
			SUM(iPhoneQty) as rgphoneqty,
			round(SUM(fServicesSale),2) as rgservicesSale , 
			round(SUM(fServicesProfit),2) as rgservicesprofit , 
			SUM(iServicesQty) as rgservicesqty,
			round(SUM(fInsuranceSale),2) as rginsuranceSale , 
			round(SUM(fInsuranceProfit),2) as rginsuranceprofit , 
			SUM(iInsuranceQty) as rginsuranceqty,
			round(SUM(fAccessorySale),2) as rgaccessorySale , 
			round(SUM(fAccessoryProfit),2) as rgaccessoryprofit , 
			SUM(iAccessoryQty) as rgaccessoryqty,
			ISNULL(round(SUM(fPhoneSale),2),0)+ISNULL(round(SUM(fServicesSale),2),0)+ISNULL(round(SUM(fInsuranceSale),2),0)+ISNULL(round(SUM(fAccessorySale),2),0)  as totalsale,
			ISNULL(round(SUM(fPhoneProfit),2),0)+ISNULL(round(SUM(fServicesProfit),2),0)+ISNULL(round(SUM(fInsuranceProfit),2),0)+ISNULL(round(SUM(fAccessoryProfit),2),0) as totalprofit,
			DATEPART(year, dtSaleDate) as yr
			from tblsale
			where 									
			dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";
				break;
		
		
		
		
			case "region":
	 $strQuery= "select * from(
			select TOP 15 DATEPART(WEEK, dtSaleDate) as week,
			round(SUM(fPhoneSale),2) as rgphoneSale , 
			round(SUM(fPhoneProfit),2) as rgphoneprofit , 
			SUM(iPhoneQty) as rgphoneqty,
			round(SUM(fServicesSale),2) as rgservicesSale , 
			round(SUM(fServicesProfit),2) as rgservicesprofit , 
			SUM(iServicesQty) as rgservicesqty,
			round(SUM(fInsuranceSale),2) as rginsuranceSale , 
			round(SUM(fInsuranceProfit),2) as rginsuranceprofit , 
			SUM(iInsuranceQty) as rginsuranceqty,
			round(SUM(fAccessorySale),2) as rgaccessorySale , 
			round(SUM(fAccessoryProfit),2) as rgaccessoryprofit , 
			SUM(iAccessoryQty) as rgaccessoryqty,
			ISNULL(round(SUM(fPhoneSale),2),0)+ISNULL(round(SUM(fServicesSale),2),0)+ISNULL(round(SUM(fInsuranceSale),2),0)+ISNULL(round(SUM(fAccessorySale),2),0)  as totalsale,
			ISNULL(round(SUM(fPhoneProfit),2),0)+ISNULL(round(SUM(fServicesProfit),2),0)+ISNULL(round(SUM(fInsuranceProfit),2),0)+ISNULL(round(SUM(fAccessoryProfit),2),0) as totalprofit,
			DATEPART(year, dtSaleDate) as yr
			from tblsale
			where 	
			rgID = $nId AND							
			dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";
				break;
				
			case "district":
								
	$strQuery= "select DATEPART(WEEK, dtSaleDate) as week,
					round(SUM(fPhoneSale),2) as rgphoneSale , 
					round(SUM(fPhoneProfit),2) as rgphoneprofit , 
					SUM(iPhoneQty) as rgphoneqty,
					round(SUM(fServicesSale),2) as rgservicesSale , 
					round(SUM(fServicesProfit),2) as rgservicesprofit , 
					SUM(iServicesQty) as rgservicesqty,
					round(SUM(fInsuranceSale),2) as rginsuranceSale , 
					round(SUM(fInsuranceProfit),2) as rginsuranceprofit , 
					SUM(iInsuranceQty) as rginsuranceqty,
					round(SUM(fAccessorySale),2) as rgaccessorySale , 
					round(SUM(fAccessoryProfit),2) as rgaccessoryprofit , 
					SUM(iAccessoryQty) as rgaccessoryqty,
					ISNULL(round(SUM(fPhoneSale),2),0)+ISNULL(round(SUM(fServicesSale),2),0)+ISNULL(round(SUM(fInsuranceSale),2),0)+ISNULL(round(SUM(fAccessorySale),2),0)  as totalsale,
					ISNULL(round(SUM(fPhoneProfit),2),0)+ISNULL(round(SUM(fServicesProfit),2),0)+ISNULL(round(SUM(fInsuranceProfit),2),0)+ISNULL(round(SUM(fAccessoryProfit),2),0) as totalprofit,
					DATEPART(year, dtSaleDate) as yr
					from tblsale
					where 	
					dstID = $nId AND							
					dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";
				break;
				
			case "store":			
											
	$strQuery= "select * from(
			select TOP 15 DATEPART(WEEK, dtSaleDate) as week,
					round(SUM(fPhoneSale),2) as rgphoneSale , 
					round(SUM(fPhoneProfit),2) as rgphoneprofit , 
					SUM(iPhoneQty) as rgphoneqty,
					round(SUM(fServicesSale),2) as rgservicesSale , 
					round(SUM(fServicesProfit),2) as rgservicesprofit , 
					SUM(iServicesQty) as rgservicesqty,
					round(SUM(fInsuranceSale),2) as rginsuranceSale , 
					round(SUM(fInsuranceProfit),2) as rginsuranceprofit , 
					SUM(iInsuranceQty) as rginsuranceqty,
					round(SUM(fAccessorySale),2) as rgaccessorySale , 
					round(SUM(fAccessoryProfit),2) as rgaccessoryprofit , 
					SUM(iAccessoryQty) as rgaccessoryqty,
					ISNULL(round(SUM(fPhoneSale),2),0)+ISNULL(round(SUM(fServicesSale),2),0)+ISNULL(round(SUM(fInsuranceSale),2),0)+ISNULL(round(SUM(fAccessorySale),2),0)  as totalsale,
					ISNULL(round(SUM(fPhoneProfit),2),0)+ISNULL(round(SUM(fServicesProfit),2),0)+ISNULL(round(SUM(fInsuranceProfit),2),0)+ISNULL(round(SUM(fAccessoryProfit),2),0) as totalprofit,
					DATEPART(year, dtSaleDate) as yr
					from tblsale
					where 	
					strID = $nId AND							
					dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";			
				break;
							
				
			case "employee":			
			
												
	$strQuery= "select * from(
			select TOP 15 DATEPART(WEEK, dtSaleDate) as week,
					round(SUM(fPhoneSale),2) as rgphoneSale , 
					round(SUM(fPhoneProfit),2) as rgphoneprofit , 
					SUM(iPhoneQty) as rgphoneqty,
					round(SUM(fServicesSale),2) as rgservicesSale , 
					round(SUM(fServicesProfit),2) as rgservicesprofit , 
					SUM(iServicesQty) as rgservicesqty,
					round(SUM(fInsuranceSale),2) as rginsuranceSale , 
					round(SUM(fInsuranceProfit),2) as rginsuranceprofit , 
					SUM(iInsuranceQty) as rginsuranceqty,
					round(SUM(fAccessorySale),2) as rgaccessorySale , 
					round(SUM(fAccessoryProfit),2) as rgaccessoryprofit , 
					SUM(iAccessoryQty) as rgaccessoryqty,
					ISNULL(round(SUM(fPhoneSale),2),0)+ISNULL(round(SUM(fServicesSale),2),0)+ISNULL(round(SUM(fInsuranceSale),2),0)+ISNULL(round(SUM(fAccessorySale),2),0)  as totalsale,
					ISNULL(round(SUM(fPhoneProfit),2),0)+ISNULL(round(SUM(fServicesProfit),2),0)+ISNULL(round(SUM(fInsuranceProfit),2),0)+ISNULL(round(SUM(fAccessoryProfit),2),0) as totalprofit,
					DATEPART(year, dtSaleDate) as yr
					from tblsale
					where 	
					empID = $nId AND							
					dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";		
				
				break;
		}
	
	$weeks_array 				= array();	
		$yearPrevious 				= explode('-',$stdate);
		$yearCurrent 				= explode('-',$enddate);																											
		$previouseYearWeekEndNumber = "";
		$currentYearStartWeekNumber = "";	
		$week_counter 				= 0;
		$arrs_Ret 					= array();
	    $startWeekNumber			= getWeekNumber($stdate);
	    $endWeekNumber				= getWeekNumber($enddate);	
		 if($yearPrevious[0]!=$yearCurrent[0])
		 {
							 #echo "Insert";	
			$previousYearEndDate		= $yearPrevious[0]."-31-12";//Year-Day-Month
			$previouseYearWeekEndNumber = getWeekNumber($previousYearEndDate);
			$currentYearStartDate		= $yearCurrent[0]."-01-01";//Year Day Month
			$currentYearStartWeekNumber = getWeekNumber($currentYearStartDate);
						 	
	  }	
		   $generatedWeeks 				= generateWeeks($startWeekNumber,$endWeekNumber,$yearPrevious[0],$yearCurrent[0],$previouseYearWeekEndNumber,$currentYearStartWeekNumber);
			
		
		$nResult = odbc_exec($conn, $strQuery);
		   if($tabular == ""){
					
				
					
			    while (odbc_fetch_row($nResult)){
			        
			       				  $weeks_array[]= odbc_result($nResult, "week");
								  $year_value = substr(odbc_result($nResult, "yr"),2);
							 	  $arrRet[odbc_result($nResult, "week")."w".$year_value]["Phone"]				= odbc_result($nResult, "rgphoneSale");
					              $arrRet[odbc_result($nResult, "week")."w".$year_value]["Services"]				= odbc_result($nResult, "rgservicesSale");
					              $arrRet[odbc_result($nResult, "week")."w".$year_value]["Insurance"]		    = odbc_result($nResult, "rginsuranceSale");
					              $arrRet[odbc_result($nResult, "week")."w".$year_value]["Accessories"]		    = odbc_result($nResult, "rgaccessorySale");
					
				}//end while
				 foreach($generatedWeeks as $generateYearKey=>$generateVal)
			   	   {
	   						if(!in_array($generateVal,$weeks_array))
							{
								  $arrRet[$generateYearKey]["Phone"]				= 0;
					              $arrRet[$generateYearKey]["Services"]				= 0;
					              $arrRet[$generateYearKey]["Insurance"]		    = 0;
					              $arrRet[$generateYearKey]["Accessories"]		    = 0;
							}
				   }
				   foreach($generatedWeeks as $generateYearKey=>$generateVal)
			  	  {				  
					  			  $arrs_Ret[$generateYearKey]["Phone"]				= $arrRet[$generateYearKey]["Phone"];
					              $arrs_Ret[$generateYearKey]["Services"]				= $arrRet[$generateYearKey]["Services"];
					              $arrs_Ret[$generateYearKey]["Insurance"]		    = $arrRet[$generateYearKey]["Insurance"];
					              $arrs_Ret[$generateYearKey]["Accessories"]		    = $arrRet[$generateYearKey]["Accessories"];
				  }
				
	     }else{
			 	
		     while (odbc_fetch_row($nResult)){
					
			          				$weeks_array[]= odbc_result($nResult, "week");
									$year_value = substr(odbc_result($nResult, "yr"),2);
						 			$arrRet[odbc_result($nResult, "week")."w".$year_value]["PhoneSale"]			       = odbc_result($nResult, "rgphoneSale");
					 				$arrRet[odbc_result($nResult, "week")."w".$year_value]["PhoneProfit"]			   = odbc_result($nResult, "rgphoneprofit");
									$arrRet[odbc_result($nResult, "week")."w".$year_value]["PhoneQuantity"]		       = odbc_result($nResult, "rgphoneqty");
					 
									$arrRet[odbc_result($nResult, "week")."w".$year_value]["ServicesSale"]		       = odbc_result($nResult, "rgservicesSale");
					 				$arrRet[odbc_result($nResult, "week")."w".$year_value]["ServicesProfit"]		   = odbc_result($nResult, "rgservicesprofit");
					 				$arrRet[odbc_result($nResult, "week")."w".$year_value]["ServicesQuantity"]		   = odbc_result($nResult, "rgservicesqty");
					 
					 
					 				$arrRet[odbc_result($nResult, "week")."w".$year_value]["InsuranceSale"]		       = odbc_result($nResult, "rginsuranceSale");
					 				$arrRet[odbc_result($nResult, "week")."w".$year_value]["InsuranceProfit"]		   = odbc_result($nResult, "rginsuranceprofit");
									$arrRet[odbc_result($nResult, "week")."w".$year_value]["InsuranceQuantity"]	       = odbc_result($nResult, "rginsuranceqty");
					 
									$arrRet[odbc_result($nResult, "week")."w".$year_value]["AccessoriesSale"]		   = odbc_result($nResult, "rgaccessorySale");
									$arrRet[odbc_result($nResult, "week")."w".$year_value]["AccessoriesProfit"]	       = odbc_result($nResult, "rgaccessoryprofit");
									$arrRet[odbc_result($nResult, "week")."w".$year_value]["AccessoriesQuantity"]	   = odbc_result($nResult, "rgaccessoryqty");
				  
		     }//end while
		   		 foreach($generatedWeeks as $generateYearKey=>$generateVal)
			   	 {
	   						if(!in_array($generateVal,$weeks_array))
							{
									$arrRet[$generateYearKey]["PhoneSale"]			       = 0;
					 				$arrRet[$generateYearKey]["PhoneProfit"]			   = 0;
									$arrRet[$generateYearKey]["PhoneQuantity"]		       = 0;
					 
									$arrRet[$generateYearKey]["ServicesSale"]		       = 0;
					 				$arrRet[$generateYearKey]["ServicesProfit"]		       = 0;
					 				$arrRet[$generateYearKey]["ServicesQuantity"]		   = 0;
					 
					 				$arrRet[$generateYearKey]["InsuranceSale"]		       = 0;
					 				$arrRet[$generateYearKey]["InsuranceProfit"]		   = 0;
									$arrRet[$generateYearKey]["InsuranceQuantity"]	       = 0;
					 
									$arrRet[$generateYearKey]["AccessoriesSale"]		   = 0;
									$arrRet[$generateYearKey]["AccessoriesProfit"]	       = 0;
									$arrRet[$generateYearKey]["AccessoriesQuantity"]	   = 0;
							}
			   }
			    foreach($generatedWeeks as $generateYearKey=>$generateVal)
			  	{
									$arrs_Ret[$generateYearKey]["PhoneSale"]			       = $arrRet[$generateYearKey]["PhoneSale"];
					 				$arrs_Ret[$generateYearKey]["PhoneProfit"]			   = $arrRet[$generateYearKey]["PhoneProfit"];
									$arrs_Ret[$generateYearKey]["PhoneQuantity"]		       = $arrRet[$generateYearKey]["PhoneQuantity"];
					 
									$arrs_Ret[$generateYearKey]["ServicesSale"]		       = $arrRet[$generateYearKey]["ServicesSale"];
					 				$arrs_Ret[$generateYearKey]["ServicesProfit"]		       = $arrRet[$generateYearKey]["ServicesProfit"];
					 				$arrs_Ret[$generateYearKey]["ServicesQuantity"]		   = $arrRet[$generateYearKey]["ServicesQuantity"];
					 
					 				$arrs_Ret[$generateYearKey]["InsuranceSale"]		       = $arrRet[$generateYearKey]["InsuranceSale"];
					 				$arrs_Ret[$generateYearKey]["InsuranceProfit"]		   = $arrRet[$generateYearKey]["InsuranceProfit"];
									$arrs_Ret[$generateYearKey]["InsuranceQuantity"]	       = $arrRet[$generateYearKey]["InsuranceQuantity"];
					 
									$arrs_Ret[$generateYearKey]["AccessoriesSale"]		   = $arrRet[$generateYearKey]["AccessoriesSale"];
									$arrs_Ret[$generateYearKey]["AccessoriesProfit"]	       = $arrRet[$generateYearKey]["AccessoriesProfit"];
									$arrs_Ret[$generateYearKey]["AccessoriesQuantity"]	   = $arrRet[$generateYearKey]["AccessoriesQuantity"];
					
				}
	   		
		  }
				 return $arrs_Ret;
				 #return $arrRet;
	
	}
	function getQuantitySoldCategWOW($strType, $nId,$tabular = "",$stdate,$enddate){
	
	global $conn;
		$arrRet = array();
		
		switch($strType)
		{
		
		case "company":
	$strQuery= "select * from(
			select TOP 15 DATEPART(WEEK, dtSaleDate) as week,
			round(SUM(fPhoneSale),2) as rgphoneSale , 
			round(SUM(fPhoneProfit),2) as rgphoneprofit , 
			SUM(iPhoneQty) as rgphoneqty,
			round(SUM(fServicesSale),2) as rgservicesSale , 
			round(SUM(fServicesProfit),2) as rgservicesprofit , 
			SUM(iServicesQty) as rgservicesqty,
			round(SUM(fInsuranceSale),2) as rginsuranceSale , 
			round(SUM(fInsuranceProfit),2) as rginsuranceprofit , 
			SUM(iInsuranceQty) as rginsuranceqty,
			round(SUM(fAccessorySale),2) as rgaccessorySale , 
			round(SUM(fAccessoryProfit),2) as rgaccessoryprofit , 
			SUM(iAccessoryQty) as rgaccessoryqty,
			ISNULL(round(SUM(fPhoneSale),2),0)+ISNULL(round(SUM(fServicesSale),2),0)+ISNULL(round(SUM(fInsuranceSale),2),0)+ISNULL(round(SUM(fAccessorySale),2),0)  as totalsale,
			ISNULL(round(SUM(fPhoneProfit),2),0)+ISNULL(round(SUM(fServicesProfit),2),0)+ISNULL(round(SUM(fInsuranceProfit),2),0)+ISNULL(round(SUM(fAccessoryProfit),2),0) as totalprofit,
			DATEPART(year, dtSaleDate) as yr
			from tblsale
			where 										
			dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";
				break;
		
		
		
		
		
			case "region":
	$strQuery= "select * from(
			select TOP 15 DATEPART(WEEK, dtSaleDate) as week,
			round(SUM(fPhoneSale),2) as rgphoneSale , 
			round(SUM(fPhoneProfit),2) as rgphoneprofit , 
			SUM(iPhoneQty) as rgphoneqty,
			round(SUM(fServicesSale),2) as rgservicesSale , 
			round(SUM(fServicesProfit),2) as rgservicesprofit , 
			SUM(iServicesQty) as rgservicesqty,
			round(SUM(fInsuranceSale),2) as rginsuranceSale , 
			round(SUM(fInsuranceProfit),2) as rginsuranceprofit , 
			SUM(iInsuranceQty) as rginsuranceqty,
			round(SUM(fAccessorySale),2) as rgaccessorySale , 
			round(SUM(fAccessoryProfit),2) as rgaccessoryprofit , 
			SUM(iAccessoryQty) as rgaccessoryqty,
			ISNULL(round(SUM(fPhoneSale),2),0)+ISNULL(round(SUM(fServicesSale),2),0)+ISNULL(round(SUM(fInsuranceSale),2),0)+ISNULL(round(SUM(fAccessorySale),2),0)  as totalsale,
			ISNULL(round(SUM(fPhoneProfit),2),0)+ISNULL(round(SUM(fServicesProfit),2),0)+ISNULL(round(SUM(fInsuranceProfit),2),0)+ISNULL(round(SUM(fAccessoryProfit),2),0) as totalprofit,
			DATEPART(year, dtSaleDate) as yr
			from tblsale
			where 	
			rgID = $nId AND							
			dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";
				break;
				
			case "district":
								
	$strQuery= "select * from(
			select TOP 15 DATEPART(WEEK, dtSaleDate) as week,
					round(SUM(fPhoneSale),2) as rgphoneSale , 
					round(SUM(fPhoneProfit),2) as rgphoneprofit , 
					SUM(iPhoneQty) as rgphoneqty,
					round(SUM(fServicesSale),2) as rgservicesSale , 
					round(SUM(fServicesProfit),2) as rgservicesprofit , 
					SUM(iServicesQty) as rgservicesqty,
					round(SUM(fInsuranceSale),2) as rginsuranceSale , 
					round(SUM(fInsuranceProfit),2) as rginsuranceprofit , 
					SUM(iInsuranceQty) as rginsuranceqty,
					round(SUM(fAccessorySale),2) as rgaccessorySale , 
					round(SUM(fAccessoryProfit),2) as rgaccessoryprofit , 
					SUM(iAccessoryQty) as rgaccessoryqty,
					ISNULL(round(SUM(fPhoneSale),2),0)+ISNULL(round(SUM(fServicesSale),2),0)+ISNULL(round(SUM(fInsuranceSale),2),0)+ISNULL(round(SUM(fAccessorySale),2),0)  as totalsale,
					ISNULL(round(SUM(fPhoneProfit),2),0)+ISNULL(round(SUM(fServicesProfit),2),0)+ISNULL(round(SUM(fInsuranceProfit),2),0)+ISNULL(round(SUM(fAccessoryProfit),2),0) as totalprofit,
					DATEPART(year, dtSaleDate) as yr
					from tblsale
					where 	
					dstID = $nId AND							
					dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";
				break;
				
			case "store":			
											
	$strQuery= "select * from(
			select TOP 15 DATEPART(WEEK, dtSaleDate) as week,
					round(SUM(fPhoneSale),2) as rgphoneSale , 
					round(SUM(fPhoneProfit),2) as rgphoneprofit , 
					SUM(iPhoneQty) as rgphoneqty,
					round(SUM(fServicesSale),2) as rgservicesSale , 
					round(SUM(fServicesProfit),2) as rgservicesprofit , 
					SUM(iServicesQty) as rgservicesqty,
					round(SUM(fInsuranceSale),2) as rginsuranceSale , 
					round(SUM(fInsuranceProfit),2) as rginsuranceprofit , 
					SUM(iInsuranceQty) as rginsuranceqty,
					round(SUM(fAccessorySale),2) as rgaccessorySale , 
					round(SUM(fAccessoryProfit),2) as rgaccessoryprofit , 
					SUM(iAccessoryQty) as rgaccessoryqty,
					ISNULL(round(SUM(fPhoneSale),2),0)+ISNULL(round(SUM(fServicesSale),2),0)+ISNULL(round(SUM(fInsuranceSale),2),0)+ISNULL(round(SUM(fAccessorySale),2),0)  as totalsale,
					ISNULL(round(SUM(fPhoneProfit),2),0)+ISNULL(round(SUM(fServicesProfit),2),0)+ISNULL(round(SUM(fInsuranceProfit),2),0)+ISNULL(round(SUM(fAccessoryProfit),2),0) as totalprofit,
					DATEPART(year, dtSaleDate) as yr
					from tblsale
					where 	
					strID = $nId AND							
					dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";			
				break;
							
				
			case "employee":			
			
												
	$strQuery= "select * from(
			select TOP 15 DATEPART(WEEK, dtSaleDate) as week,
					round(SUM(fPhoneSale),2) as rgphoneSale , 
					round(SUM(fPhoneProfit),2) as rgphoneprofit , 
					SUM(iPhoneQty) as rgphoneqty,
					round(SUM(fServicesSale),2) as rgservicesSale , 
					round(SUM(fServicesProfit),2) as rgservicesprofit , 
					SUM(iServicesQty) as rgservicesqty,
					round(SUM(fInsuranceSale),2) as rginsuranceSale , 
					round(SUM(fInsuranceProfit),2) as rginsuranceprofit , 
					SUM(iInsuranceQty) as rginsuranceqty,
					round(SUM(fAccessorySale),2) as rgaccessorySale , 
					round(SUM(fAccessoryProfit),2) as rgaccessoryprofit , 
					SUM(iAccessoryQty) as rgaccessoryqty,
					ISNULL(round(SUM(fPhoneSale),2),0)+ISNULL(round(SUM(fServicesSale),2),0)+ISNULL(round(SUM(fInsuranceSale),2),0)+ISNULL(round(SUM(fAccessorySale),2),0)  as totalsale,
					ISNULL(round(SUM(fPhoneProfit),2),0)+ISNULL(round(SUM(fServicesProfit),2),0)+ISNULL(round(SUM(fInsuranceProfit),2),0)+ISNULL(round(SUM(fAccessoryProfit),2),0) as totalprofit,
					DATEPART(year, dtSaleDate) as yr
					from tblsale
					where 	
					empID = $nId AND							
					dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";		
				
				break;
		}
	
		$weeks_array 				= array();	
		$yearPrevious 				= explode('-',$stdate);
		$yearCurrent 				= explode('-',$enddate);																											
		$previouseYearWeekEndNumber = "";
		$currentYearStartWeekNumber = "";	
		$week_counter 				= 0;
		$arrs_Ret 					= array();
	    $startWeekNumber			= getWeekNumber($stdate);
	    $endWeekNumber				= getWeekNumber($enddate);	
		 if($yearPrevious[0]!=$yearCurrent[0])
		 {
							 #echo "Insert";	
			$previousYearEndDate		= $yearPrevious[0]."-31-12";//Year-Day-Month
			$previouseYearWeekEndNumber = getWeekNumber($previousYearEndDate);
			$currentYearStartDate		= $yearCurrent[0]."-01-01";//Year Day Month
			$currentYearStartWeekNumber = getWeekNumber($currentYearStartDate);
						 	
	  }	
		   $generatedWeeks 				= generateWeeks($startWeekNumber,$endWeekNumber,$yearPrevious[0],$yearCurrent[0],$previouseYearWeekEndNumber,$currentYearStartWeekNumber);
										
		
		$nResult = odbc_exec($conn, $strQuery);
		        if($tabular == ""){
					
					
					
			     while (odbc_fetch_row($nResult)){
						//$week[odbc_result($nResult, "week")] = true ;
						$weeks_array[]= odbc_result($nResult, "week");
						$year_value = substr(odbc_result($nResult, "yr"),2);
						$arrRet[odbc_result($nResult, "week")."w".$year_value]['Phone']		= odbc_result($nResult, "rgphoneqty");
						$arrRet[odbc_result($nResult, "week")."w".$year_value]['Services']		= odbc_result($nResult, "rgservicesqty");
						$arrRet[odbc_result($nResult, "week")."w".$year_value]['Insurance']	= odbc_result($nResult, "rginsuranceqty");
						$arrRet[odbc_result($nResult, "week")."w".$year_value]['Accessories']  = odbc_result($nResult, "rgaccessoryqty"); 
				}//end while
				 foreach($generatedWeeks as $generateYearKey=>$generateVal)
			   	 {
	   						if(!in_array($generateVal,$weeks_array))
							{
								$arrRet[$generateYearKey]['Phone']		    = 0;
								$arrRet[$generateYearKey]['Services']		= 0;
								$arrRet[$generateYearKey]['Insurance']		= 0;
								$arrRet[$generateYearKey]['Accessories']    = 0; 
							}
				  }
				  foreach($generatedWeeks as $generateYearKey=>$generateVal)
			  	  {
				  
			       				$arrs_Ret[$generateYearKey]['Phone']		    = $arrRet[$generateYearKey]['Phone'];
								$arrs_Ret[$generateYearKey]['Services']		= $arrRet[$generateYearKey]['Services'];
								$arrs_Ret[$generateYearKey]['Insurance']		= $arrRet[$generateYearKey]['Insurance'];
								$arrs_Ret[$generateYearKey]['Accessories']    = $arrRet[$generateYearKey]['Accessories']; 
				 }
				
				
		}else{
				
				
					
			     while (odbc_fetch_row($nResult))
				 {
			        
			     
			      
			        
						//$week[odbc_result($nResult, "week")] = true ;
						$weeks_array[]= odbc_result($nResult, "week");
						 $year_value = substr(odbc_result($nResult, "yr"),2);
						 $arrRet[odbc_result($nResult, "week")."w".$year_value]["PhoneSale"]				= odbc_result($nResult, "rgphoneSale");
					 	 $arrRet[odbc_result($nResult, "week")."w".$year_value]["PhoneProfit"]				= odbc_result($nResult, "rgphoneprofit");
						 $arrRet[odbc_result($nResult, "week")."w".$year_value]["PhoneQuantity"]			= odbc_result($nResult, "rgphoneqty");
					 
					 	 $arrRet[odbc_result($nResult, "week")."w".$year_value]["ServicesSale"]				= odbc_result($nResult, "rgservicesSale");
					 	 $arrRet[odbc_result($nResult, "week")."w".$year_value]["ServicesProfit"]			= odbc_result($nResult, "rgservicesprofit");
					 	 $arrRet[odbc_result($nResult, "week")."w".$year_value]["ServicesQuantity"]			= odbc_result($nResult, "rgservicesqty");
					 
					 	 $arrRet[odbc_result($nResult, "week")."w".$year_value]["InsuranceSale"]		    = odbc_result($nResult, "rginsuranceSale");
					 	 $arrRet[odbc_result($nResult, "week")."w".$year_value]["InsuranceProfit"]			= odbc_result($nResult, "rginsuranceprofit");
					 	 $arrRet[odbc_result($nResult, "week")."w".$year_value]["InsuranceQuantity"]		= odbc_result($nResult, "rginsuranceqty");
					 
						$arrRet[odbc_result($nResult, "week")."w".$year_value]["AccessoriesSale"]		    = odbc_result($nResult, "rgaccessorySale");
						$arrRet[odbc_result($nResult, "week")."w".$year_value]["AccessoriesProfit"]		    = odbc_result($nResult, "rgaccessoryprofit");
						$arrRet[odbc_result($nResult, "week")."w".$year_value]["AccessoriesQuantity"]		    = odbc_result($nResult, "rgaccessoryqty");
					
					
		         }//end while
				  foreach($generatedWeeks as $generateYearKey=>$generateVal)
			   	   {
	   						if(!in_array($generateVal,$weeks_array))
							{
								$arrRet[$generateYearKey]["PhoneSale"]					= 0;
					 			 $arrRet[$generateYearKey]["PhoneProfit"]				= 0;
								 $arrRet[$generateYearKey]["PhoneQuantity"]				= 0;
					 
					 			 $arrRet[$generateYearKey]["ServicesSale"]				= 0;
					 	 		$arrRet[$generateYearKey]["ServicesProfit"]				= 0;
					 			 $arrRet[$generateYearKey]["ServicesQuantity"]			= 0;
					 
					 	 		$arrRet[$generateYearKey]["InsuranceSale"]		    	= 0;
					 			$arrRet[$generateYearKey]["InsuranceProfit"]			= 0;
					 	 		$arrRet[$generateYearKey]["InsuranceQuantity"]			= 0;
					 
								$arrRet[$generateYearKey]["AccessoriesSale"]		    = 0;
								$arrRet[$generateYearKey]["AccessoriesProfit"]		    = 0;
								$arrRet[$generateYearKey]["AccessoriesQuantity"]		= 0;
							}
				   }
				    foreach($generatedWeeks as $generateYearKey=>$generateVal)
			  	  {
					  			$arrs_Ret[$generateYearKey]["PhoneSale"]					= $arrRet[$generateYearKey]["PhoneSale"]	;
					 			 $arrs_Ret[$generateYearKey]["PhoneProfit"]				= $arrRet[$generateYearKey]["PhoneProfit"];
								 $arrs_Ret[$generateYearKey]["PhoneQuantity"]				= $arrRet[$generateYearKey]["PhoneQuantity"];
					 
					 			 $arrs_Ret[$generateYearKey]["ServicesSale"]				= $arrRet[$generateYearKey]["ServicesSale"];
					 	 		$arrs_Ret[$generateYearKey]["ServicesProfit"]				= $arrRet[$generateYearKey]["ServicesProfit"];
					 			 $arrs_Ret[$generateYearKey]["ServicesQuantity"]			= $arrRet[$generateYearKey]["ServicesQuantity"];
					 
					 	 		$arrs_Ret[$generateYearKey]["InsuranceSale"]		    	= $arrRet[$generateYearKey]["InsuranceSale"];
					 			$arrs_Ret[$generateYearKey]["InsuranceProfit"]			= $arrRet[$generateYearKey]["InsuranceProfit"];
					 	 		$arrs_Ret[$generateYearKey]["InsuranceQuantity"]			= $arrRet[$generateYearKey]["InsuranceQuantity"];
					 
								$arrs_Ret[$generateYearKey]["AccessoriesSale"]		    = $arrRet[$generateYearKey]["AccessoriesSale"];
								$arrs_Ret[$generateYearKey]["AccessoriesProfit"]		    = $arrRet[$generateYearKey]["AccessoriesProfit"];
								$arrs_Ret[$generateYearKey]["AccessoriesQuantity"]		= $arrRet[$generateYearKey]["AccessoriesQuantity"];
				  }
				 
	}	 		
				  return $arrs_Ret; 
				 #return $arrRet;
}


function getProfitbyMarginSalesWOW($strType, $nId,$tabular = "",$stdate,$enddate)
	{
		global $conn;
		$arrRet = array();
		
		switch($strType)
		{
		
		case "company":
	$strQuery= "select * from(
			select TOP 15 DATEPART(WEEK, dtSaleDate) as week,
			round(SUM(fPhoneSale),2) as rgphoneSale , 
			round(SUM(fPhoneProfit),2) as rgphoneprofit , 
			SUM(iPhoneQty) as rgphoneqty,
			round(SUM(fServicesSale),2) as rgservicesSale , 
			round(SUM(fServicesProfit),2) as rgservicesprofit , 
			SUM(iServicesQty) as rgservicesqty,
			round(SUM(fInsuranceSale),2) as rginsuranceSale , 
			round(SUM(fInsuranceProfit),2) as rginsuranceprofit , 
			SUM(iInsuranceQty) as rginsuranceqty,
			round(SUM(fAccessorySale),2) as rgaccessorySale , 
			round(SUM(fAccessoryProfit),2) as rgaccessoryprofit , 
			SUM(iAccessoryQty) as rgaccessoryqty,
			ISNULL(round(SUM(fPhoneSale),2),0)+ISNULL(round(SUM(fServicesSale),2),0)+ISNULL(round(SUM(fInsuranceSale),2),0)+ISNULL(round(SUM(fAccessorySale),2),0)  as totalsale,
			ISNULL(round(SUM(fPhoneProfit),2),0)+ISNULL(round(SUM(fServicesProfit),2),0)+ISNULL(round(SUM(fInsuranceProfit),2),0)+ISNULL(round(SUM(fAccessoryProfit),2),0) as totalprofit,
			DATEPART(year, dtSaleDate) as yr
			from tblsale
			where 									
			dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";
				break;
		
		
			case "region":
	$strQuery= "select * from(
			select TOP 15 DATEPART(WEEK, dtSaleDate) as week,
			round(SUM(fPhoneSale),2) as rgphoneSale , 
			round(SUM(fPhoneProfit),2) as rgphoneprofit , 
			SUM(iPhoneQty) as rgphoneqty,
			round(SUM(fServicesSale),2) as rgservicesSale , 
			round(SUM(fServicesProfit),2) as rgservicesprofit , 
			SUM(iServicesQty) as rgservicesqty,
			round(SUM(fInsuranceSale),2) as rginsuranceSale , 
			round(SUM(fInsuranceProfit),2) as rginsuranceprofit , 
			SUM(iInsuranceQty) as rginsuranceqty,
			round(SUM(fAccessorySale),2) as rgaccessorySale , 
			round(SUM(fAccessoryProfit),2) as rgaccessoryprofit , 
			SUM(iAccessoryQty) as rgaccessoryqty,
			ISNULL(round(SUM(fPhoneSale),2),0)+ISNULL(round(SUM(fServicesSale),2),0)+ISNULL(round(SUM(fInsuranceSale),2),0)+ISNULL(round(SUM(fAccessorySale),2),0)  as totalsale,
			ISNULL(round(SUM(fPhoneProfit),2),0)+ISNULL(round(SUM(fServicesProfit),2),0)+ISNULL(round(SUM(fInsuranceProfit),2),0)+ISNULL(round(SUM(fAccessoryProfit),2),0) as totalprofit,
			DATEPART(year, dtSaleDate) as yr
			from tblsale
			where 	
			rgID = $nId AND							
			dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";
				break;
				
			case "district":
								
	$strQuery= "select * from(
			select TOP 15 DATEPART(WEEK, dtSaleDate) as week,
					round(SUM(fPhoneSale),2) as rgphoneSale , 
					round(SUM(fPhoneProfit),2) as rgphoneprofit , 
					SUM(iPhoneQty) as rgphoneqty,
					round(SUM(fServicesSale),2) as rgservicesSale , 
					round(SUM(fServicesProfit),2) as rgservicesprofit , 
					SUM(iServicesQty) as rgservicesqty,
					round(SUM(fInsuranceSale),2) as rginsuranceSale , 
					round(SUM(fInsuranceProfit),2) as rginsuranceprofit , 
					SUM(iInsuranceQty) as rginsuranceqty,
					round(SUM(fAccessorySale),2) as rgaccessorySale , 
					round(SUM(fAccessoryProfit),2) as rgaccessoryprofit , 
					SUM(iAccessoryQty) as rgaccessoryqty,
					ISNULL(round(SUM(fPhoneSale),2),0)+ISNULL(round(SUM(fServicesSale),2),0)+ISNULL(round(SUM(fInsuranceSale),2),0)+ISNULL(round(SUM(fAccessorySale),2),0)  as totalsale,
					ISNULL(round(SUM(fPhoneProfit),2),0)+ISNULL(round(SUM(fServicesProfit),2),0)+ISNULL(round(SUM(fInsuranceProfit),2),0)+ISNULL(round(SUM(fAccessoryProfit),2),0) as totalprofit,
					DATEPART(year, dtSaleDate) as yr
					from tblsale
					where 	
					dstID = $nId AND							
				dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";
				break;
				
			case "store":			
											
	$strQuery= "select * from(
			select TOP 15 DATEPART(WEEK, dtSaleDate) as week,
					round(SUM(fPhoneSale),2) as rgphoneSale , 
					round(SUM(fPhoneProfit),2) as rgphoneprofit , 
					SUM(iPhoneQty) as rgphoneqty,
					round(SUM(fServicesSale),2) as rgservicesSale , 
					round(SUM(fServicesProfit),2) as rgservicesprofit , 
					SUM(iServicesQty) as rgservicesqty,
					round(SUM(fInsuranceSale),2) as rginsuranceSale , 
					round(SUM(fInsuranceProfit),2) as rginsuranceprofit , 
					SUM(iInsuranceQty) as rginsuranceqty,
					round(SUM(fAccessorySale),2) as rgaccessorySale , 
					round(SUM(fAccessoryProfit),2) as rgaccessoryprofit , 
					SUM(iAccessoryQty) as rgaccessoryqty,
					ISNULL(round(SUM(fPhoneSale),2),0)+ISNULL(round(SUM(fServicesSale),2),0)+ISNULL(round(SUM(fInsuranceSale),2),0)+ISNULL(round(SUM(fAccessorySale),2),0)  as totalsale,
					ISNULL(round(SUM(fPhoneProfit),2),0)+ISNULL(round(SUM(fServicesProfit),2),0)+ISNULL(round(SUM(fInsuranceProfit),2),0)+ISNULL(round(SUM(fAccessoryProfit),2),0) as totalprofit,
					DATEPART(year, dtSaleDate) as yr
					from tblsale
					where 	
					strID = $nId AND							
					dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";			
				break;
							
				
			case "employee":			
			
												
	$strQuery= "select * from(
			select TOP 15 DATEPART(WEEK, dtSaleDate) as week,
					round(SUM(fPhoneSale),2) as rgphoneSale , 
					round(SUM(fPhoneProfit),2) as rgphoneprofit , 
					SUM(iPhoneQty) as rgphoneqty,
					round(SUM(fServicesSale),2) as rgservicesSale , 
					round(SUM(fServicesProfit),2) as rgservicesprofit , 
					SUM(iServicesQty) as rgservicesqty,
					round(SUM(fInsuranceSale),2) as rginsuranceSale , 
					round(SUM(fInsuranceProfit),2) as rginsuranceprofit , 
					SUM(iInsuranceQty) as rginsuranceqty,
					round(SUM(fAccessorySale),2) as rgaccessorySale , 
					round(SUM(fAccessoryProfit),2) as rgaccessoryprofit , 
					SUM(iAccessoryQty) as rgaccessoryqty,
					ISNULL(round(SUM(fPhoneSale),2),0)+ISNULL(round(SUM(fServicesSale),2),0)+ISNULL(round(SUM(fInsuranceSale),2),0)+ISNULL(round(SUM(fAccessorySale),2),0)  as totalsale,
					ISNULL(round(SUM(fPhoneProfit),2),0)+ISNULL(round(SUM(fServicesProfit),2),0)+ISNULL(round(SUM(fInsuranceProfit),2),0)+ISNULL(round(SUM(fAccessoryProfit),2),0) as totalprofit,
					DATEPART(year, dtSaleDate) as yr
					from tblsale
					where 	
					empID = $nId AND							
					dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";		
				
				break;
		}
		$weeks_array 				= array();	
		$yearPrevious 				= explode('-',$stdate);
		$yearCurrent 				= explode('-',$enddate);																											
		$previouseYearWeekEndNumber = "";
		$currentYearStartWeekNumber = "";	
		$week_counter 				= 0;
		$arrs_Ret 					= array();
	    $startWeekNumber			= getWeekNumber($stdate);
	    $endWeekNumber				= getWeekNumber($enddate);	
		 if($yearPrevious[0]!=$yearCurrent[0])
		 {
							 #echo "Insert";	
			$previousYearEndDate		= $yearPrevious[0]."-31-12";//Year-Day-Month
			$previouseYearWeekEndNumber = getWeekNumber($previousYearEndDate);
			$currentYearStartDate		= $yearCurrent[0]."-01-01";//Year Day Month
			$currentYearStartWeekNumber = getWeekNumber($currentYearStartDate);
						 	
	  }	
		   $generatedWeeks 				= generateWeeks($startWeekNumber,$endWeekNumber,$yearPrevious[0],$yearCurrent[0],$previouseYearWeekEndNumber,$currentYearStartWeekNumber);
										
		
		$nResult = odbc_exec($conn, $strQuery);
		        if($tabular == ""){
					
				
					
			        while (odbc_fetch_row($nResult)){
			        
			       
					 
			        
						//$week[odbc_result($nResult, "week")] = true ;
						$weeks_array[]= odbc_result($nResult, "week");
						 $year_value = substr(odbc_result($nResult, "yr"),2);
						 $rgphoneSale    = 	 odbc_result($nResult, "rgphoneSale");
				    				 $rgphoneprofit  = 	  odbc_result($nResult, "rgphoneprofit");
					 
							if(isset($rgphoneSale) && round($rgphoneSale)!=0 && $rgphoneSale!="" && isset($rgphoneprofit) && round($rgphoneprofit)!=0 && $rgphoneprofit!=""){
	 $arrRet[odbc_result($nResult, "week")."w".$year_value]["Phone"] =  round(($rgphoneprofit/$rgphoneSale)*100);
							}else{
							 $arrRet[odbc_result($nResult, "week")."w".$year_value]["Phone"]  = 0;
							}
					 $rgservicesSale = odbc_result($nResult, "rgservicesSale");
					 $rgservicesprofit = odbc_result($nResult, "rgservicesprofit");
					 		if(isset($rgservicesSale) && round($rgservicesSale)!=0 && $rgservicesSale!="" && isset($rgservicesprofit) && round($rgservicesprofit)!=0 && $rgservicesprofit!=""){
	$arrRet[odbc_result($nResult, "week")."w".$year_value]["Services"] =  round(($rgservicesprofit/$rgservicesSale)*100);
							}else{
							 $arrRet[odbc_result($nResult, "week")."w".$year_value]["Services"]  = 0;
							}
				    $rginsuranceSale	 =  odbc_result($nResult, "rginsuranceSale");
				    $rginsuranceprofit = 	  odbc_result($nResult, "rginsuranceprofit");
					
					 if(isset($rginsuranceSale) && round($rginsuranceSale)!=0 && $rginsuranceSale!="" && isset($rginsuranceprofit) && round($rginsuranceprofit)!=0 && $rginsuranceprofit!=""){
	 $arrRet[odbc_result($nResult, "week")."w".$year_value]["Insurance"] = round(($rginsuranceprofit/$rginsuranceSale)*100);
							}else{
							 $arrRet[odbc_result($nResult, "week")."w".$year_value]["Insurance"]  = 0;
							}
					 $rgaccessorySale   = odbc_result($nResult, "rgaccessorySale");
					 $rgaccessoryprofit = odbc_result($nResult, "rgaccessoryprofit");
					

					if(isset($rgaccessorySale) && round($rgaccessorySale)!=0 && $rgaccessorySale!="" && isset($rgaccessoryprofit) && $rgaccessoryprofit!=0 && $rgaccessoryprofit!=""){
	 $arrRet[odbc_result($nResult, "week")."w".$year_value]["Accessories"] = round(($rgaccessoryprofit/$rgaccessorySale)*100);
							}else{
							 $arrRet[odbc_result($nResult, "week")."w".$year_value]["Accessories"]  = 0;
							}
					 
					}//end while
					 foreach($generatedWeeks as $generateYearKey=>$generateVal)
			   	    {
	   						if(!in_array($generateVal,$weeks_array))
							{
								$arrRet[$generateYearKey]["Phone"]  = 0;
								$arrRet[$generateYearKey]["Services"]  = 0;
								$arrRet[$generateYearKey]["Insurance"]  = 0;
								$arrRet[$generateYearKey]["Accessories"]  = 0;
								
							}
				   }
				    foreach($generatedWeeks as $generateYearKey=>$generateVal)
			  	   {
					   			$arrs_Ret[$generateYearKey]["Phone"]  		    = $arrRet[$generateYearKey]["Phone"];
								$arrs_Ret[$generateYearKey]["Services"]   	    = $arrRet[$generateYearKey]["Services"];
								$arrs_Ret[$generateYearKey]["Insurance"]  	    = $arrRet[$generateYearKey]["Insurance"];
								$arrs_Ret[$generateYearKey]["Accessories"]  	= $arrRet[$generateYearKey]["Accessories"];
				   }
					  
					
	            }else{
				
					
			        while (odbc_fetch_row($nResult)){
						//$week[odbc_result($nResult, "week")] = true ;
						$weeks_array[]= odbc_result($nResult, "week");
						$year_value = substr(odbc_result($nResult, "yr"),2);
						$arrRet[odbc_result($nResult, "week")."w".$year_value]["PhoneSale"]					= odbc_result($nResult, "rgphoneSale");
						$arrRet[odbc_result($nResult, "week")."w".$year_value]["PhoneProfit"]				= odbc_result($nResult, "rgphoneprofit");
						$arrRet[odbc_result($nResult, "week")."w".$year_value]["PhoneQuantity"]				= odbc_result($nResult, "rgphoneqty");
		 
						 $arrRet[odbc_result($nResult, "week")."w".$year_value]["ServicesSale"]				= odbc_result($nResult, "rgservicesSale");
						 $arrRet[odbc_result($nResult, "week")."w".$year_value]["ServicesProfit"]			= odbc_result($nResult, "rgservicesprofit");
						 $arrRet[odbc_result($nResult, "week")."w".$year_value]["ServicesQuantity"]			= odbc_result($nResult, "rgservicesqty");
				 
						$arrRet[odbc_result($nResult, "week")."w".$year_value]["InsuranceSale"]		    	= odbc_result($nResult, "rginsuranceSale");
						$arrRet[odbc_result($nResult, "week")."w".$year_value]["InsuranceProfit"]			= odbc_result($nResult, "rginsuranceprofit");
						$arrRet[odbc_result($nResult, "week")."w".$year_value]["InsuranceQuantity"]			= odbc_result($nResult, "rginsuranceqty");
				 
						$arrRet[odbc_result($nResult, "week")."w".$year_value]["AccessoriesSale"]		    = odbc_result($nResult, "rgaccessorySale");
						$arrRet[odbc_result($nResult, "week")."w".$year_value]["AccessoriesProfit"]		    = odbc_result($nResult, "rgaccessoryprofit");
						$arrRet[odbc_result($nResult, "week")."w".$year_value]["AccessoriesQuantity"]		= odbc_result($nResult, "rgaccessoryqty");
					
					
		         }//end while
				  foreach($generatedWeeks as $generateYearKey=>$generateVal)
			   	  {
	   						if(!in_array($generateVal,$weeks_array))
							{
								$arrRet[$generateYearKey]["PhoneSale"]					= 0;
								$arrRet[$generateYearKey]["PhoneProfit"]				= 0;
								$arrRet[$generateYearKey]["PhoneQuantity"]				= 0;
		 
						 		$arrRet[$generateYearKey]["ServicesSale"]				= 0;
						 		$arrRet[$generateYearKey]["ServicesProfit"]			    = 0;
						 		$arrRet[$generateYearKey]["ServicesQuantity"]			= 0;
				 
								$arrRet[$generateYearKey]["InsuranceSale"]		    	= 0;
								$arrRet[$generateYearKey]["InsuranceProfit"]			= 0;
								$arrRet[$generateYearKey]["InsuranceQuantity"]			= 0;
				 
								$arrRet[$generateYearKey]["AccessoriesSale"]		    = 0;
								$arrRet[$generateYearKey]["AccessoriesProfit"]		    = 0;
								$arrRet[$generateYearKey]["AccessoriesQuantity"]		= 0;
								
							}
				  }
				   foreach($generatedWeeks as $generateYearKey=>$generateVal)
			  	  {
					  			$arrs_Ret[$generateYearKey]["PhoneSale"]					= $arrRet[$generateYearKey]["PhoneSale"];
								$arrs_Ret[$generateYearKey]["PhoneProfit"]				    = $arrRet[$generateYearKey]["PhoneProfit"];
								$arrs_Ret[$generateYearKey]["PhoneQuantity"]				= $arrRet[$generateYearKey]["PhoneQuantity"];
				 
								$arrs_Ret[$generateYearKey]["ServicesSale"]				    = $arrRet[$generateYearKey]["ServicesSale"];
								$arrs_Ret[$generateYearKey]["ServicesProfit"]			    = $arrRet[$generateYearKey]["ServicesProfit"];
								$arrs_Ret[$generateYearKey]["ServicesQuantity"]			    = $arrRet[$generateYearKey]["ServicesQuantity"];
						 
								$arrs_Ret[$generateYearKey]["InsuranceSale"]		    	= $arrRet[$generateYearKey]["InsuranceSale"];
								$arrs_Ret[$generateYearKey]["InsuranceProfit"]			    = $arrRet[$generateYearKey]["InsuranceProfit"];
								$arrs_Ret[$generateYearKey]["InsuranceQuantity"]			= $arrRet[$generateYearKey]["InsuranceQuantity"];
								
								$arrs_Ret[$generateYearKey]["AccessoriesSale"]		        = $arrRet[$generateYearKey]["AccessoriesSale"];
								$arrs_Ret[$generateYearKey]["AccessoriesProfit"]		    = $arrRet[$generateYearKey]["AccessoriesProfit"];
								$arrs_Ret[$generateYearKey]["AccessoriesQuantity"]		    = $arrRet[$generateYearKey]["AccessoriesQuantity"];
					  
					  
				  }
							 
				/*echo "<pre>";
				print_r($arrs_Ret);
				echo "</pre>";*/
	   
				}
				  return $arrs_Ret;
				 #return $arrRet;
}





function getGrossSalesWOW2($strType, $nId,$tabular = "",$dtDate)
	{
		global $conn;
		$arrRet = array();
		
		switch($strType)
		{
		
		case "company":
	$strQuery= "select * from(
			select TOP 15 DATEPART(WEEK, dtSaleDate) as week,
			round(SUM(fPhoneSale),2) as rgphoneSale , 
			round(SUM(fPhoneProfit),2) as rgphoneprofit , 
			SUM(iPhoneQty) as rgphoneqty,
			round(SUM(fServicesSale),2) as rgservicesSale , 
			round(SUM(fServicesProfit),2) as rgservicesprofit , 
			SUM(iServicesQty) as rgservicesqty,
			round(SUM(fInsuranceSale),2) as rginsuranceSale , 
			round(SUM(fInsuranceProfit),2) as rginsuranceprofit , 
			SUM(iInsuranceQty) as rginsuranceqty,
			round(SUM(fAccessorySale),2) as rgaccessorySale , 
			round(SUM(fAccessoryProfit),2) as rgaccessoryprofit , 
			SUM(iAccessoryQty) as rgaccessoryqty,
			ISNULL(round(SUM(fPhoneSale),2),0)+ISNULL(round(SUM(fServicesSale),2),0)+ISNULL(round(SUM(fInsuranceSale),2),0)+ISNULL(round(SUM(fAccessorySale),2),0)  as totalsale,
			ISNULL(round(SUM(fPhoneProfit),2),0)+ISNULL(round(SUM(fServicesProfit),2),0)+ISNULL(round(SUM(fInsuranceProfit),2),0)+ISNULL(round(SUM(fAccessoryProfit),2),0) as totalprofit,
			DATEPART(year, dtSaleDate) as yr
			from tblsale
			where
				dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";
				break;
		
			case "region":
	$strQuery= "select * from(
			select TOP 15 DATEPART(WEEK, dtSaleDate) as week,
			round(SUM(fPhoneSale),2) as rgphoneSale , 
			round(SUM(fPhoneProfit),2) as rgphoneprofit , 
			SUM(iPhoneQty) as rgphoneqty,
			round(SUM(fServicesSale),2) as rgservicesSale , 
			round(SUM(fServicesProfit),2) as rgservicesprofit , 
			SUM(iServicesQty) as rgservicesqty,
			round(SUM(fInsuranceSale),2) as rginsuranceSale , 
			round(SUM(fInsuranceProfit),2) as rginsuranceprofit , 
			SUM(iInsuranceQty) as rginsuranceqty,
			round(SUM(fAccessorySale),2) as rgaccessorySale , 
			round(SUM(fAccessoryProfit),2) as rgaccessoryprofit , 
			SUM(iAccessoryQty) as rgaccessoryqty,
			ISNULL(round(SUM(fPhoneSale),2),0)+ISNULL(round(SUM(fServicesSale),2),0)+ISNULL(round(SUM(fInsuranceSale),2),0)+ISNULL(round(SUM(fAccessorySale),2),0)  as totalsale,
			ISNULL(round(SUM(fPhoneProfit),2),0)+ISNULL(round(SUM(fServicesProfit),2),0)+ISNULL(round(SUM(fInsuranceProfit),2),0)+ISNULL(round(SUM(fAccessoryProfit),2),0) as totalprofit,
			DATEPART(year, dtSaleDate) as yr
			from tblsale
			where 	
			rgID = $nId AND							
			dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";
				break;
				
			case "district":
								
	$strQuery= "select * from(
			select TOP 15 DATEPART(WEEK, dtSaleDate) as week,
					round(SUM(fPhoneSale),2) as rgphoneSale , 
					round(SUM(fPhoneProfit),2) as rgphoneprofit , 
					SUM(iPhoneQty) as rgphoneqty,
					round(SUM(fServicesSale),2) as rgservicesSale , 
					round(SUM(fServicesProfit),2) as rgservicesprofit , 
					SUM(iServicesQty) as rgservicesqty,
					round(SUM(fInsuranceSale),2) as rginsuranceSale , 
					round(SUM(fInsuranceProfit),2) as rginsuranceprofit , 
					SUM(iInsuranceQty) as rginsuranceqty,
					round(SUM(fAccessorySale),2) as rgaccessorySale , 
					round(SUM(fAccessoryProfit),2) as rgaccessoryprofit , 
					SUM(iAccessoryQty) as rgaccessoryqty,
					ISNULL(round(SUM(fPhoneSale),2),0)+ISNULL(round(SUM(fServicesSale),2),0)+ISNULL(round(SUM(fInsuranceSale),2),0)+ISNULL(round(SUM(fAccessorySale),2),0)  as totalsale,
					ISNULL(round(SUM(fPhoneProfit),2),0)+ISNULL(round(SUM(fServicesProfit),2),0)+ISNULL(round(SUM(fInsuranceProfit),2),0)+ISNULL(round(SUM(fAccessoryProfit),2),0) as totalprofit,
					DATEPART(year, dtSaleDate) as yr
					from tblsale
					where 	
					dstID = $nId AND							
					dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";
				break;
				
			case "store":			
											
	$strQuery= "select * from(
			select TOP 15 DATEPART(WEEK, dtSaleDate) as week,
					round(SUM(fPhoneSale),2) as rgphoneSale , 
					round(SUM(fPhoneProfit),2) as rgphoneprofit , 
					SUM(iPhoneQty) as rgphoneqty,
					round(SUM(fServicesSale),2) as rgservicesSale , 
					round(SUM(fServicesProfit),2) as rgservicesprofit , 
					SUM(iServicesQty) as rgservicesqty,
					round(SUM(fInsuranceSale),2) as rginsuranceSale , 
					round(SUM(fInsuranceProfit),2) as rginsuranceprofit , 
					SUM(iInsuranceQty) as rginsuranceqty,
					round(SUM(fAccessorySale),2) as rgaccessorySale , 
					round(SUM(fAccessoryProfit),2) as rgaccessoryprofit , 
					SUM(iAccessoryQty) as rgaccessoryqty,
					ISNULL(round(SUM(fPhoneSale),2),0)+ISNULL(round(SUM(fServicesSale),2),0)+ISNULL(round(SUM(fInsuranceSale),2),0)+ISNULL(round(SUM(fAccessorySale),2),0)  as totalsale,
					ISNULL(round(SUM(fPhoneProfit),2),0)+ISNULL(round(SUM(fServicesProfit),2),0)+ISNULL(round(SUM(fInsuranceProfit),2),0)+ISNULL(round(SUM(fAccessoryProfit),2),0) as totalprofit,
					DATEPART(year, dtSaleDate) as yr
					from tblsale
					where 	
					strID = $nId AND							
					dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";			
				break;
							
				
			case "employee":			
			
												
	$strQuery= "select * from(
			select TOP 15 DATEPART(WEEK, dtSaleDate) as week,
					round(SUM(fPhoneSale),2) as rgphoneSale , 
					round(SUM(fPhoneProfit),2) as rgphoneprofit , 
					SUM(iPhoneQty) as rgphoneqty,
					round(SUM(fServicesSale),2) as rgservicesSale , 
					round(SUM(fServicesProfit),2) as rgservicesprofit , 
					SUM(iServicesQty) as rgservicesqty,
					round(SUM(fInsuranceSale),2) as rginsuranceSale , 
					round(SUM(fInsuranceProfit),2) as rginsuranceprofit , 
					SUM(iInsuranceQty) as rginsuranceqty,
					round(SUM(fAccessorySale),2) as rgaccessorySale , 
					round(SUM(fAccessoryProfit),2) as rgaccessoryprofit , 
					SUM(iAccessoryQty) as rgaccessoryqty,
					ISNULL(round(SUM(fPhoneSale),2),0)+ISNULL(round(SUM(fServicesSale),2),0)+ISNULL(round(SUM(fInsuranceSale),2),0)+ISNULL(round(SUM(fAccessorySale),2),0)  as totalsale,
					ISNULL(round(SUM(fPhoneProfit),2),0)+ISNULL(round(SUM(fServicesProfit),2),0)+ISNULL(round(SUM(fInsuranceProfit),2),0)+ISNULL(round(SUM(fAccessoryProfit),2),0) as totalprofit,
					DATEPART(year, dtSaleDate) as yr
					from tblsale
					where 	
					empID = $nId AND							
					dtSaleDate between  CONVERT( datetime, '$stdate', 105) 
	 and    CONVERT( datetime, '$enddate', 105) 
			group by DATEPART(WEEK, dtSaleDate), DATEPART(year, dtSaleDate)
			order by yr desc,week desc) as tbl1
			order by yr asc,week asc";		
				
				break;
		}
	
		
		        if($tabular == ""){
					
					
			        while (odbc_fetch_row($nResult)){
						//$week[odbc_result($nResult, "week")] = true ;
						$year_value = substr(odbc_result($nResult, "yr"),2);
						 $arrRet[odbc_result($nResult, "week")."w".$year_value]["Sales"]				= odbc_result($nResult, "totalsale");
						 $arrRet[odbc_result($nResult, "week")."w".$year_value]["Profit"]				= odbc_result($nResult, "totalprofit");
			        
				    }//end while
				    	
				 
				
				    	 
	            }else{
				while (odbc_fetch_row($nResult)){			
					 $arrRet[odbc_result($nResult, "week")]["PhoneSale"]				= odbc_result($nResult, "rgphoneSale");
					 $arrRet[odbc_result($nResult, "week")]["PhoneProfit"]				= odbc_result($nResult, "rgphoneprofit");
					 $arrRet[odbc_result($nResult, "week")]["PhoneQuantity"]			= odbc_result($nResult, "rgphoneqty");
					 
					 
					 $arrRet[odbc_result($nResult, "week")]["ServicesSale"]				= odbc_result($nResult, "rgservicesSale");
					 $arrRet[odbc_result($nResult, "week")]["ServicesProfit"]			= odbc_result($nResult, "rgservicesprofit");
					 $arrRet[odbc_result($nResult, "week")]["ServicesQuantity"]			= odbc_result($nResult, "rgservicesqty");
					 
					 
					 
					 $arrRet[odbc_result($nResult, "week")]["InsuranceSale"]		    = odbc_result($nResult, "rginsuranceSale");
					 $arrRet[odbc_result($nResult, "week")]["InsuranceProfit"]			= odbc_result($nResult, "rginsuranceprofit");
					 $arrRet[odbc_result($nResult, "week")]["InsuranceQuantity"]		= odbc_result($nResult, "rginsuranceqty");
					 
					 
					$arrRet[odbc_result($nResult, "week")]["AccessoriesSale"]		    = odbc_result($nResult, "rgaccessorySale");
					$arrRet[odbc_result($nResult, "week")]["AccessoriesProfit"]		    = odbc_result($nResult, "rgaccessoryprofit");
					$arrRet[odbc_result($nResult, "week")]["AccessoriesQuantity"]		    = odbc_result($nResult, "rgaccessoryqty");
					
		         }
	   

	   		 
					 
					
		
		
	
				}


				
				 return $arrRet;
}

function getWeekNumber($date)
{
	
	global $conn;
		$arrRet = array();




 $strQuery="SELECT DATEPART(wk,CONVERT( datetime, '".$date."', 105)) AS weekNumber";
$nResult = odbc_exec($conn, $strQuery);
while (odbc_fetch_row($nResult)){
	$weekno = odbc_result($nResult, "weekNumber");
}
	
	return $weekno;

}
function generateWeeks($startWeekNumber,$endWeekNumber,$yearPrevious,$yearCurrent,$previouseYearWeekEndNumber="",$currentYearStartWeekNumber="")
{
	$weeksArr = array();
	/*echo "startWeekNumber=".$startWeekNumber."--";
		echo "previouseYearWeekEndNumber=".$previouseYearWeekEndNumber."--";
		echo "currentYearStartWeekNumber=".$currentYearStartWeekNumber."--";
		echo "endWeekNumber=".$endWeekNumber."--";
		echo "yearPrevious=".$yearPrevious."--";
		echo "yearCurrent=".$yearCurrent."--";*/
		
	if($previouseYearWeekEndNumber=="" && $currentYearStartWeekNumber=="")
	{		
		for($i=$startWeekNumber;$i<=$endWeekNumber;$i++)
		{
			$nowYear = substr($yearCurrent,2);
			$weeksArr[$i."w".$nowYear]=$i;
		}
	}
	else
	{
		
		
	
		for($j=$startWeekNumber;$j<=$previouseYearWeekEndNumber;$j++)
		{
			$nowYear = substr($yearPrevious,2);
			$weeksArr[$j."w".$nowYear]=$j;
		}
		for($k=$currentYearStartWeekNumber;$k<=$endWeekNumber;$k++)
		{
			$nowYear = substr($yearCurrent,2);
			$weeksArr[$k."w".$nowYear]=$k;
		}
		
	
	}
	#print_r($weeksArr);
	return $weeksArr;

}
function getDateFromWeekYear($weekNumber,$year,$days)
{		global $conn;
	echo $strQuery="DECLARE @StartDate DATE, 
			  @WeekVal INT,
			  @year nvarchar(53)
			  set  @year = 2011
			  SET  @WeekVal =  53
			  SET  @StartDate = '1/1/'+@year
			  ;WITH cte AS (
			  SELECT @StartDate AS DateVal, DATEPART(wk, @StartDate) AS WeekVal, 1 AS RowVal
			  UNION ALL
			  SELECT DATEADD(d, 1, DateVal),  DATEPART(wk, DATEADD(d, 1, DateVal)), RowVal + 1 
			  FROM cte WHERE RowVal < 365
			)
			SELECT MIN(DateVal) StartOfWeek
			FROM cte
			WHERE WeekVal = @WeekVal
			OPTION (MAXRECURSION 366)";
			
		$nResult = odbc_exec($conn, $strQuery);
		print_r($nResult);
		
		      #$a = odbc_fetch_row($nResult);
			  #print_r($a);
			  
			  
			  /*$rc = odbc_fetch_into(7, $a);*/
					
				$dateOfWeek = "";
					
			       #$nResult = odbc_exec($conn, $strQuery);
				  # print_r(odbc_fetch_into($nResult,$row));
			while (odbc_fetch_into($nResult,$row))
			{	#	echo "--------------------------------------------------------------------";
					echo $row[0]; 
				#$dateOfWeek = odbc_result($nResult, "StartOfWeek");
				}
					return $dateOfWeek;
}









?>