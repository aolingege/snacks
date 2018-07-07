###RABC0
-权限模型的基础
-1.用户(User) 2.角色(Role) 3.许可(Permission) 4.会话(Session)
-用户和角色是多对多的关系，表示一个用户在不同的场景下可以拥有不同的角色。
-用五张表可以建立经典的RBAC0关系。
-用户表(User),用户角色关系(UserRoleRelation),角色(Role),角色许可关系(RolePRMSRelation),许可(Permission)

###RABC1
-在角色的基础上进行分层，具有面向对象的思想，引入了继承的关系。

###RABC2
-RBAC2，也是基于RBAC0模型的基础上，进行了角色的访问控制。
-角色与角色之间产生了互斥的关系,权限也是有限的。

###RABC3
-RABC1与RABC2的综合。