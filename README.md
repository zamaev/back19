Простой движок

# Model


## Get Entitiy
`$model->user(2);`	- return Entity object by `id`

`$model->user(['name' => 'user1']);` - return Entity object by WHERE

`$model->user()` - return empty Entity for create new table row

`$data = $model->user()->setData()->save()` - create new Entity, set date and save with get data