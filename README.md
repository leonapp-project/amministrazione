
# API
## api/enableBuyTickets.php
### Tipo: GET  
### Parametri:
- OAuth: String(32)
- student_id: Int
- can_buy_tickets: Bool
### Description
Abilita lo studente a comprare le focaccine. Come al solito, se il parametro OAuth non viene specificato, si controlla il cookie OAuth_key.
### Permessi OAuth necessari
- administration.students.enableBuyTickets
### Esempio
https://amministrazione.leonapp.it/api/enableBuyTickets.php?student_id=1&can_buy_tickets=1&OAuth=[OAUTH_KEY_HERE]
### Resituzione
```json
{
    "exit": "success",
    "message": "Student can_buy_tickets value changed"
}
```
## api/updateStudentInfo.php
### Tipo: POST
### Parametri:
- OAuth: String(32)
- field: String ["first_name", "last_name", "email", "class_section", "class_number", "can_buy_tickets"]
- value: String
### Description
Aggiorna le informazioni di uno studente.
Come al solito, se il parametro OAuth non viene specificato, si controlla il cookie OAuth_key.
### Permessi OAuth necessari
- administration.studenti.updateInfo
### Esempio
https://amministrazione.leonapp.it/api/updateStudentInfo.php?id=1&field=first_name&value=Leonardo&OAuth=[OAUTH_KEY_HERE]
### Resituzione
```json
{
    "exit": "success",
    "message": "Student info updated"
}
```

## api/getStudentList.php
### Tipo: GET
### Parametri:
- OAuth: String(32)
- sortBy: String ["nome", "cognome"]
- sort: String ["ASC", "DESC"]
- limit: Int
- startBy: Int
- nome: (Optional) String
- cognome: (Optional) String
- anno: (Optional) Int
- classe: (Optional) String

### Description
Restituisce la lista degli studenti. 
sortBy e sort servono a chiedere uno specifico criterio di ordinamento. 
limit e startBy servono a chiedere una specifica porzione della lista (nel sito il limit è 30).
I parametri nome, cognome, anno e classe servono a filtrare la lista e sono opzionali. Se vengono specificati, si
effettua una selezione di tutti gli elementi corrispondenti ai parametri specificati.  
Come al solito, se il parametro OAuth non viene specificato, si controlla il cookie OAuth_key.
### Permessi OAuth necessari
- administration.studenti.getList

### Esempio
https://amministrazione.leonapp.it/api/getList.php?sortBy=nome&sort=ASC&limit=30&startBy=0&nome=Leonardo&cognome=Di%20Caprio&anno=5&classe=SCA&OAuth=[OAUTH_KEY_HERE]
### Resituzione
```json
{
    "exit":"success",
    "results": 1,
    "students":
    [
        {
            "id": 1,
            "first_name": "Leonardo",
            "last_name": "Di Caprio",
            "email": "leonardo.dicaprio@studentilicei.leonexiii.it",
            "last_login": "2023-02-26 12:46:05.128475",
            "date_joined": "2023-02-26 12:44:12.683224",
            "class_section": "SCA",
            "can_buy_tickets": 0,
            "username": "troytheplayboy",
            "class_number": "5"
        }
    ]
}
```

## api/getStudentInfo.php
### Tipo: GET
### Parametri:
- OAuth: String(32)
- id: Int
### Description
Restituisce le informazioni di uno studente a partire dal suo id numerico.
Come al solito, se il parametro OAuth non viene specificato, si controlla il cookie OAuth_key.
### Permessi OAuth necessari
- administration.studenti.getInfo
### Esempio
https://amministrazione.leonapp.it/api/getStudentInfo.php?id=1&OAuth=[OAUTH_KEY_HERE]
### Restituzione
```json
{
    "exit":"success",
    "student":
    {
        "id": 1,
        "first_name": "Leonardo",
        "last_name": "Di Caprio",
        "email": "le",
        "last_login": "2023-02-26 12:46:05.128475",
        "date_joined": "2023-02-26 12:44:12.683224",
        "class_section": "SCA",
    }
}
```

## api/downloadTicketsBought.php
### Tipo: GET
### Parametri:
- OAuth: String(32)
- mode: String ["CSV", "JSON"]
- id: Int
- email (Optional): String
### Description
Restituisce la lista dei biglietti di uno studente a partire dal suo id numerico o dalla sua email.
Come al solito, se il parametro OAuth non viene specificato, si controlla il cookie OAuth_key.
### Permessi OAuth necessari
- administration.studenti.getTicketsBought
### Esempio
https://amministrazione.leonapp.it/api/downloadTicketsBought.php?mode=JSON&id=1&OAuth=[OAUTH_KEY_HERE]
### Restituzione
```json
{
  "exit": "success",
  "data": {
    "03/2023": 1
  }
}```

## Permessi
- administration.studenti.getList --> Bool
- administration.students.enableBuyTickets --> Bool
- administration.studenti.getInfo --> Bool
- administration.studenti.getTicketsBought --> Bool
- administration.studenti.updateInfo --> Bool
