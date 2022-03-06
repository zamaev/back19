Простой движок

# Model


## Get Entitiy
`model()->user(2);`	- return Entity object by `id`

`model()->user(['name' => 'user1']);` - return Entity object by WHERE

`model()->user()` - return empty Entity for create new table row

`$data = model()->user()->setData($data)->save()` - create new Entity, set date and save with get data

## Get Query with Table

`model()->users` - return Query object with table

`model()->users()` - return Query object with table

`model()->users([...])` - return Query object with `table` and `where` params


`model()->users->update(1, ['name'=>'test']` - Query update