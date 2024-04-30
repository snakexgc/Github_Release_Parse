# Github_Release_Parse
![image](https://github.com/snakexgc/Github_Release_Parse/assets/78722169/25a5f739-940b-4165-8ccf-33fd6b62d719)

# 项目介绍
本项目存在的意义是方便一部分 **不会魔法上网，但是又有从github项目的Release下载文件的需求** 的用户，你只需要知道该项目的完整地址，将链接粘贴到输入框，勾选使用加速，你就能获取到该项目的最新的latest标签和Pre-release标签下面的包含加速的下载链接，点击链接即可加速下载。

# 如何使用？
1. 访问网站: [https://github.20010101.xyz/](https://github.20010101.xyz/)
2. 输入你要下载Release的github仓库地址，并勾选 **是否使用加速？**
3. 点击开始解析

![image](https://github.com/snakexgc/Github_Release_Parse/assets/78722169/40bf683e-89fe-41fc-a703-15a9b13911d0)
然后你就会得到如下界面

![image](https://github.com/snakexgc/Github_Release_Parse/assets/78722169/235d03f4-3027-4f9b-bb3b-ea678ecf4678)
点击你需要的版本即可下载

# 自行部署
一共就两个文件，你只需要在一个有PHP环境的服务器上创建一个网页，将这连个文件放入即可，index.html是主网页，parse_github.php文件是逻辑处理的。

# 关于加速
加速站是我自建在Cloudflare Workers上的Github加速站点 
站点地址： [https://git.20010101.xyz](https://git.20010101.xyz) 
由于使用的是数字域名，部分地区的部分运营商可能存在阻断的情况。
