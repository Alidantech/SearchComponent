# Search Component Documentation

This is a database search component. It enables you to search data from your database. by just providing the database configurations and then you enter a keyword and you recieve the results.

## Dependencies

- Database

### Requires Interface

1. Requires a `configured database`.
2. Requires database configuration parameters inside a `db.env` file:
    ```.env
    #ADD your database configurations here.
      DB_HOST = 'host'
      DB_USER = 'username'
      DB_PASSWORD = 'password'
      DB_NAME = 'name'
    ```
3. Requires the `table` and `columns` to search from.
4. Requires a `Keyword` to perform the search.

### Provides Interface

- The search component provides a connection to the database based on the provided configurations.
- It returns search results from the database in JSON format.
- Provides a method to search from a single table.
- Provides a method to search from multiple tables.



