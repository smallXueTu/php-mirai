# php-mirai
一个在<a href = "https://github.com/mamoe/mirai-console/">mirai-console</a>通过<a href="https://github.com/project-mirai/mirai-api-http">http api</a>运行的php sdk

使用它需要安装启动<a href = "https://github.com/mamoe/mirai-console/">mirai-console</a>
然后生成plugins文件夹将<a href="https://github.com/project-mirai/mirai-api-http">http api</a>插件放进去
重启mirai-console
安装成功后会生成plugins/MiraiAPIHTTP目录
创建setting.yml文件

然后修改plugins/MiraiAPIHTTP/setting.yml 为以下内容：
```yaml
## 该配置为全局配置，对所有Session有效

# 可选，默认值为0.0.0.0
host: '0.0.0.0'

# 可选，默认值为8080
port: 8080          

# 可选，默认由插件随机生成，建议手动指定
authKey: 1234567890  

# 可选，缓存大小，默认4096.缓存过小会导致引用回复与撤回消息失败
cacheSize: 4096

# 可选，是否开启websocket，默认关闭，建议通过Session范围的配置设置
enableWebsocket: false

# 可选，配置CORS跨域，默认为*，即允许所有域名
cors: 
  - '*'
```
添加上报服务 粘贴到setting.yml最下面:
```yaml
# 可选，上报服务
report:
  # 可选，是否启用上报，默认不启用
  enable: true
  # 可选，上报群消息的配置
  groupMessage:
    # 可选，是否上报，默认不上报
    report: true
  # 可选，上报好友消息的配置
  friendMessage:
    # 可选，是否上报，默认不上报
    report: true
  # 可选，上报临时消息的配置
  tempMessage:
    # 可选，是否上报，默认不上报
    report: true
  # 可选，上报事件消息的配置
  eventMessage:
    report: true
  # 必选，上报的地址列表
  destinations:
    - https://postman-echo.com/post
  # 可选，上报时需要带上的请求头
  extraHeaders:
    # 填上你需要的请求头（如授权信息等）
    Authorization: basic xxx
```
然后下载所有文件安装到web目录，需要php版本>=5.4
linux注意设置目录权限
然后打开config.php按注释修改即可运行。
