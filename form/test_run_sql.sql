SELECT * 
FROM Tmp_Admin_Data_Pickslips 
WHERE substr(VDATEDESP,0,10) >= :EIDBV AND substr(VDATEDESP,0,10) <= :EIDBV2