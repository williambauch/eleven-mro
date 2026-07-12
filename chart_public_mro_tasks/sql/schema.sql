SELECT status_code,
   count(*) as qtde
FROM
    "public".mro_tasks
GROUP BY
     status_code