#  Обновление индекса в sphinx #
Для добавления нового параметра  индекса :
1. Добавить в файл manticore.conf в раздел source src_films: src_common :
``` 
     sql_field_string = 'parameter_name'
``` 
``` 
     sql_query = SELECT fbpt.'parameter_name'
```
2. Добавить в Repository :
``` 
   $results = $this->container->get('sphinx')
            ->createQuery()
            ->select('id')
            ->from('name_index')
            ->match(['parameter_name'], $someParameter)
            ->getResults();
        return $results ;
``` 
3. Выполнить в контейнере sphinx :
``` 
    /usr/bin/indexer --all --rotate
``` 
4. В терминале выполнить :
``` 
    docker-compose stop sphinx
```
``` 
     docker-compose up -d
```
``` 
     docker-compose exec sphinx bash
```
