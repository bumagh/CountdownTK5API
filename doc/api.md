📄 olAPI文档
包含注册账号,登录账号,修改密码,获取所有区服,获取所有角色,管理角色,登录到大厅(建立长连接websocket),获取当前所在地图,获取当前聊天框数据,发言,点击单个角色获取角色信息,获取角色背包信息,获取角色配置信息,获取公告,战斗相关
## 1.注册账号
```json
{ 
   username:"test1",
   password:"123",
   inviteCode:"2025"
}
```
成功返回
```json
{ 
   code: 0,
   msg:"注册成功"
}
```
失败返回
```json
{ 
   code: 1,
   msg:"注册失败,账号已存在"
}
```
```json
{ 
   code: 2,
   msg:"注册失败,邀请码不存在"
}
```
## 2.登录账号
```json
{ 
   username:"test1",
   password:"123"
}
```
成功返回
```json
{ 
   code: 0,
   msg:"登录成功",
   data:{
       userToken:"xxxx",
       zoneList:[
           {
               zoneIndex:0,
               zoneId:0,
               zoneName:"蛇年大吉",
               zoneStatus: "on",
               zonePlayerCounts: 100,
               zoneTag:"新区"
           }
       ]
   }
}
```
失败返回
```json
{ 
   code: 1,
   msg:"登录失败,账号或密码错误"
}
```
```json
{ 
   code: 2,
   msg:"登录失败,账号已禁用,请联系客服"
}
```
```json
{ 
   code: 3,
   msg:"登录失败,服务器升级中,预计开服时间1月30号 10:00"
}
```
## 3.选择区服
```json
{ 
   zoneId:0
}
{
    header:{
         token:"xxx"
    }
}
```
成功返回
```json
{ 
   code: 0,
   msg:"区服登录成功",
   data:{
       characterList:[
           {
               characterId:0,
               characterName:"三国新人",
               characterCountry: "吴",
               characterLevel: 15,
               characterJob: "武士",
               characterStatus: "on"
           }
       ]
   }
}
```
失败返回
```json
{ 
   code: 1,
   msg:"登录失败,服务器爆满"
}
```
```json
{ 
   code: 2,
   msg:"登录失败,账号过期或其他错误"
}
```
## 3.选择角色
```json
{ 
   characterId:0,
   userToken:"xxx"
}
```
成功返回
```json
{ 
   code: 0,
   msg:"登录大厅成功",
   data:{
       chatList:[
           {
               chatZone:"区服"
               characterId:0,
               characterName:"三国新人",
               characterIcon:"Icon1",
               chatContent: "三国新人发言的内容"
           }
       ],
       characterInfo:{
               characterId:0,
               characterName:"三国新人",
               characterIcon:"Icon1",
               characterCountry: "吴",
               characterLevel: 15,
               characterJob: "武士",
               characterCoin: 1000,
               mapLocation:"新手村",
               soldierList:[
                   {
                       soldierId:1
                       soldierName:"吕布",
                       soldierStatus: "on"
                   }
               ],
               characterStatus: "on",
               chatStatus: "on",
               inFighting:"off"
       },
       
   }
}
```
失败返回
```json
{ 
   code: 1,
   msg:"登录失败,角色禁用"
}
```
```json
{ 
   code: 2,
   msg:"登录失败,账号过期或其他错误"
}
```
# 游戏结算
# 