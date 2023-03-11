<?php
//Path: api/downloadTicketsBought.php
/* MySQL tables
utenti
id, first_name, last_name, email, last_login, date_joined, class_branch, class_section, can_buy_tickets, username

OAuth
id, type, grade, okey, expiration, access_to, commento
*/
/* 
this is an API api/getStudentData.php?
OAuth: 32 chars
then one of the two:
- id: int (id of the student) 
- field: string (field to update)
- value: string (value to update)
The OAuth grade must be less than or equal to 1. It must not be expired (expiration is a timestamp). 
The access_to, which is a JSON, should have the key/permission administration.studenti.updateInfo set to true or
any of the roots set to true for example administration.studenti.* or administration.* or *.
*/


?>