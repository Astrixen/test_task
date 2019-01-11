drop table users;
create temp table users(id bigserial, group_id bigint);
insert into users(group_id) values (1), (1), (1), (2), (1), (3);
select 
	MIN(id) as min_id, 
	group_id, 
	count(group_id) as count 
FROM (
	select 
		id, 
		group_id, 
		sum(border) over (order by id) as group_num
	from (
		select 
			id, 
			group_id,
			CASE 
				when (lag(group_id) OVER (ORDER BY id ASC) = group_id) then 0
				else 1
			end as border
		from users
		) users_divided
	)users_group_num
group by group_num, group_id
