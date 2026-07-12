SELECT
   id,
   inserted_date,
   username,
   application,
   creator,
   ip_user,
   "action",
   description
FROM
   sc_log
ORDER BY 
    id DESC