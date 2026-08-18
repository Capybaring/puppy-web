# iPet 主题开发文档 — 内容缺失 & 购物车样式排查

> 状态说明：这是历史排查记录。当前主题已改为从 WordPress 后台管理媒体、分类、页面和菜单，并已移除虚拟页面、自动创建分类和主题内置媒体逻辑。实际配置以 `README.md` 为准。

日期:2026-08-08
更新:2026-08-08 阶段A已完成——购物车 8 轮历史 CSS(原 926-1995 行区间)已合并为一份连续规则,文末的行号对照表已失效,仅作历史记录保留。合并过程中额外发现并修复了一个真实 bug:某一层的 `border: 0 !important` 把行分隔线的宽度/样式重置没了,后面几层只改了颜色、从没恢复显示,导致购物车商品行之间实际上没有任何分隔线——已恢复为 1px 实线。
更新:2026-08-08 阶段B已完成(在阶段A的基础上做真正的扁平化改版,`style.css` 里搜 "Cart page — flattened" 定位)——主容器去掉了圆角/边框/阴影/浅蓝底色,不再是一张卡片,靠标题下方的分隔线和行间分隔线来划分区域;侧边栏的总价/优惠券/推荐商品从三张独立阴影卡片合并成一个整体区域,内部用 1px 分隔线分节;数量控件旁边原来是一个 28px 的方形图标按钮(和 WooCommerce 实际渲染的文字链接对不上),现在改成步进器下方一个普通的"Remove"文字链接。
更新:2026-08-08 阶段C已完成(`style.css` 里搜 "Checkout page (Phase C)" 定位)——结算页之前几乎零定制(只有 2 条 `#payment` 规则),现在补齐了两栏布局、步骤间分隔线、表单控件边框圆角、订单汇总侧边栏(复用购物车侧边栏同一套"一体化区域+内部分隔线"处理)、下单按钮样式。**这一步是按 WooCommerce Blocks 结算页(`wc-block-checkout__*`)的标准类名写的,没有实际渲染验证过**——如果这个站用的是经典(非区块)结算页,选择器会不生效,需要告诉我改用经典结算页的类名重做。
更新:2026-08-08 阶段D已完成——`functions.php` 的 `puppy_market_virtual_pages()` 重写了,5 个说明页(About/Contact/Shipping/Returns/Privacy policy)从"一句占位文案"变成"简介+多个分节标题"的结构,内容和站内其他地方已经在说的承诺保持一致(运费满 $75 免运、365 天可退),分节之间用 hairline 分隔线,和购物车/结算页的扁平风格统一(不是单独一套卡片样式)。这些是我直接生成的正式文案,不是占位符了,但涉及实际法律责任的部分(尤其隐私政策、退货条款的具体条款)建议你/法务再过一遍,我写的是通用、合理但不具约束力保证的版本。
更新:2026-08-08 阶段E部分完成——商品详情页评分分布条(原来写死 75/0/13/0/13,和下面列出的真实评论对不上)已经改成从真实评论数据计算;平均分星星图标(原来写死"4实心+1空心"不管实际分数是多少)也改成按真实平均分取整显示。首页畅销榜 `orderby` 从 `popularity` 改成 `date` 过渡。**分类挂商品、设置促销价、跑测试订单这几项本质是后台/数据库操作,不是代码能解决的,我这边没有能力直接执行,需要你在 WooCommerce 后台手动做。**
更新:2026-08-08 阶段F已完成——检查了 `header.php` 和 `footer.php`,导航和页脚里都没有指向博客/Journal 的入口,`archive.php`/`single.php` 目前是死代码(没有实际入口能到达),不需要额外处理;如果以后要做内容运营再考虑接回导航。
更新:2026-08-08 新一轮排查(第四节)——按更严格的标准("除颜色和少量可替代内容如医药外,其余结构/布局/组件都要和 Chewy 一致")重新过了一遍全站,发现了阶段 0-7、A-F 都没覆盖到的结构性缺口,主要集中在:商品详情页缺 Autoship/订阅购买模式和面包屑、商品卡只有单徽章、Header 没有购物车预览抽屉和搜索联想、"我的账户"页面还是旧的胶囊圆角风格且 CSS 有和当年购物车一样的重复堆叠问题。详见第四节,已给出阶段 H-L 的修改建议,等你确认后再动手。
更新:2026-08-08 阶段H已完成,但先做了一处修正——重新核对代码后发现商品详情页其实**已经有面包屑**:主题没有单独的 `single-product.php`,商品页是走 `woocommerce.php` 这个 WooCommerce 官方约定的"总模板"渲染的,而这个文件顶部无条件调用了 `woocommerce_breadcrumb()`,所以面包屑本来就在生效,第四节里"没有面包屑"的判断是错的,已经不需要再补。实际做的是另外两项:①`functions.php` 里加了 `puppy_market_product_purchase_options()`(挂在 `woocommerce_before_add_to_cart_button`),在加购按钮上方渲染"One-time purchase / Autoship & Save 5%"两个选项,选中 Autoship 会通过 `woocommerce_add_cart_item_data`+`woocommerce_before_calculate_totals` 给这一行商品真实打 95 折(不是纯装饰的假 UI),购物车/结算页里也会用 `woocommerce_get_item_data` 显示"Autoship & Save (5% off)"标签——**但这只是下单时的一次性折扣,不是真正按周期自动扣款的订阅,要做到"每隔几周自动扣一次款"需要装 WooCommerce Subscriptions 这个付费插件,现在的实现是结构和视觉上对齐 Chewy,不是完整的订阅计费系统**;②加了 `puppy_market_product_delivery_estimate()`,在价格下方显示"In stock — get it by <日期>"或"Currently out of stock",日期用 `strtotime('+3 weekdays')` 简单估算,不是接了真实物流系统。样式方面 `style.css` 里 `single-product` 那块桌面端 grid-template-areas 加了一行 `delivery`,新增了 `.ipet-purchase-options`/`.ipet-purchase-option`/`.ipet-delivery-estimate` 规则,复用了已有的 line/green/gray-soft 这几个 token,没有引入新配色。另外顺手发现 `style.css` 里已经有一套没人用的 `.account-dropdown`/`.cart-dropdown`/`.header-dropdown` 死 CSS(搜了所有 PHP 文件都没有对应标签),猜测是更早一版设计留下的,阶段 J 做购物车预览抽屉时可以直接复用这套规则,不用从零写。
更新:2026-08-08 阶段I已完成——`template-parts/product-card.php` 原来只接受一个 `badge` 字符串,现在改成徽章数组:显式传入的 `badge`/`badges` 会和"自动追加的 Autoship & Save 徽章"合并去重,最多显示 3 个,叠放在图片左上角(不再是单一丝带),复用阶段 H 里同一条"是否符合 Autoship 资格"的判断(可购买+有货+简单商品类型)。价格行下方新增一行"$X.XX w/ Autoship"折扣价提示,数字来自和阶段 H 同一个 95 折算法,不是另外编的假数字。`content-product.php`(商城网格)和 `front-page.php`(首页各个商品轮播)都是通过这一个组件复用的,所以改一处、全站商品卡都同步更新,不需要逐个页面改。CSS 改动都在 `style.css` 搜 "Phase 3: shared product-card component" 那块附近,复用了已有的 ink/muted/line/green token。
更新:2026-08-08 阶段J已完成——`header.php` 里账号/购物车原来是两个纯链接,现在每个都在 `.header-menu` 容器里加了一个 `.header-dropdown` 悬浮面板:账号面板显示"Sign In/My Account"大按钮+登录状态相关的次要链接;购物车面板是真正的 mini-cart 预览,显示购物车里前 3 件商品的缩略图/名称/数量单价、还差多少到 $75 免运费、以及"View cart"/"Checkout"两个按钮。这套 `.header-dropdown`/`.account-dropdown`/`.cart-dropdown`/`.dropdown-button` 相关 CSS 之前其实已经写好了但没有任何 PHP 用到(`style.css` 里的死代码,搜了全部 PHP 文件确认没有对应标签),这次直接复用,只新增了商品缩略图列表(`.cart-dropdown-items` 等)这部分之前没有覆盖到的样式。因为悬停展开在触屏设备上不生效,`footer.php` 里加了一段 JS,用 `matchMedia('(hover: hover)')` 检测,触屏上第一次点击只展开面板、不跳转,再点一次或点面板里的按钮才真正跳转。顺手修了一个和这次改动相关的旧 bug:之前"加入购物车"成功后更新头部数字的 JS(`front-page.php`/`footer.php` 里都有一份)写的是往 `<strong>Cart</strong>` 里塞"Cart (N)"文字,但实际数字一直是显示在旁边独立的 `.cart-count` 上,两边对不上,现在统一改成只更新 `.cart-count`。**导航里没有新加类目**,把原来的"Pet Care"链接文案改成了"Grooming & Wellness"、位置摆在 Chewy 放 Pharmacy 入口的那个位置——这是用真实存在的美容/护理内容替代医药类目,不是新造了一个假类目。**这版 mini-cart 是页面加载时的快照,加购后只更新数量数字,面板里的商品列表要等下次刷新页面才会同步**,如果要做到实时刷新预览列表,需要接 WooCommerce 的 fragments 接口,目前还没做。
更新:2026-08-08 阶段K已完成,过程中发现"我的账户"的技术债比预想的更严重——不是简单的"胶囊圆角没改",而是和当年购物车一样的"多轮改版互相打架"问题,只是规模更小:`style.css` 里同时存在三套针对同一批 MyAccount 选择器的规则:①最早一版用 `.woocommerce-account`(不带 `body` 前缀),配色用的是主题自己的 ink/green/muted token,导航链接 999px 圆角、内容区 20px 圆角;②后来又加了一版 `body.woocommerce-account`(多了 body 这个类型选择器,优先级比①高),配色是一套和全站其它地方都不一样的蓝色(`#4b9fc4`/`#2f7fa7`/`#234057` 等),边框+阴影的卡片式外观;③阶段1(全局密度调整)又加了第三层,试图把①的圆角从 999px/20px 改小,但因为选择器优先级不如②,实际没有生效。也就是说,**这个页面实际渲染出来的一直是②那套蓝色卡片样式,不是主题自己的绿色调**,阶段1当时以为改成功了,其实没生效。这次把三套规则合并成一份:布局和"实际在渲染"的②保持一致(导航在左、内容在右),但把蓝色 hex 值全部换成了主题已有的 --ink/--green/--muted/--line token——**这不是我自己选了个新颜色,是把这块散落的、和全站其它地方都对不上的蓝色,统一回全站本来就在用的同一套配色变量**,和阶段A修复购物车分隔线 bug 是同一类"发现即修复"的技术债处理,不是配色决策。同时把导航/内容区的边框+阴影卡片外观去掉,改成一条 1px 竖线分隔左右两栏,和购物车/结算页统一用的"分隔线代替卡片盒子"是同一套语言;登录/注册表单原来是一张 20px 圆角白卡片,现在也去掉了卡片外观,和 Chewy 的登录页一样就是纯页面内容,没有额外的盒子包裹。
更新:2026-08-08 阶段L已完成(次要补充)——①商城页侧栏加了"Customer rating"筛选(4星以上/3星以上单选),`functions.php` 里 `puppy_market_catalog_query()` 新增了对 `_wc_average_rating` 的 meta_query;顺手发现侧栏筛选(`.shop-sidebar .puppy-filter-*`)也是和"我的账户"同一批遗留的蓝色调(`#234057`/`#5c7180`/`#4b9fc4` 等),一并换成了主题自己的 token,原因和阶段K一样。②排序下拉(`.woocommerce-ordering select`)圆角并进了阶段1那批"6px 扁平化"选择器列表里,**但排序/计数这两个是 WooCommerce 默认输出,没有真实 WordPress 环境我没法截图确认实际渲染效果,这条只能算"代码层面对齐",建议你上线后自己看一眼**。③页脚加了一行支付方式标识(Visa/Mastercard/Amex/PayPal/Apple Pay,文字chip,不是抓的真实卡组织logo图片)。**没有加 App Store/Google Play 下载徽章**——footer 原文写着"A personal WooCommerce storefront in progress",这个项目目前没有真实 App,加下载徽章等于暗示有一个不存在的手机应用,所以这条我没做,如果以后真的做了 App 再补。④首页加了"Shop by brand"品牌墙,复用商城侧栏同一个品牌来源(优先读真实 product_brand/product_tag 分类法,没有数据才落到 Purina/Friskies/Royal Canin/Whiskas/Hill's/Blue Buffalo 这几个常见品牌名的文字入口),两处共用了新加的 `puppy_market_common_brands()` 函数,不是各写一份。这几项加完,阶段 H-L 全部完成,`docs/chewy-alignment-todo.md` 第四节里列的缺口目前只剩"mini-cart 实时刷新预览列表(需要接 fragments 接口)"和"结算页选择器未做实机验证"这两条还没处理,其余分类挂商品/促销价/测试订单/真实评论这几项本质是后台操作,一直都需要你在 wp-admin 里手动完成。
范围:检查现有主题代码,定位「页面无内容」和「购物车页面样式与 Chewy 不一致」两个问题的根因,给出可执行的修改清单。本文档不涉及配色方案本身,聚焦内容/结构/CSS 技术债。
更新:2026-08-09 全站代码去重清理——你让我检查全部代码有没有重复/错误/可简化的地方,查出来的问题比预期大很多,详见文末新增的「五、全站代码去重清理」一节。简单说:`style.css` 里差不多 95% 的独立选择器都至少被重复定义过一次,本质是这个主题经历过大约 8-9 轮完整的首页/头部重做,每一轮的旧 CSS 都从来没删过,只是新一轮追加在后面靠更高优先级/更靠后的位置盖过去——和当年购物车、我的账户遇到的问题是同一种模式,只是这次是整份文件级别的。写了个脚本把"同一个选择器在同一个媒体查询条件下出现了不止一次"的情况(一共 308 组)都合并成一份,合并前后重新计算了每个选择器在每个断点下"实际会生效的最终样式",逐条比对确认了两边完全一致(1079 个选择器/断点组合,0 处不一致)才落盘,不是凭感觉删的。文件从 2062 行降到大约 1360 行左右。同时清理了 4 个从来没被调用/挂载过的死函数(`puppy_market_single_product_perks`、`puppy_market_product_promos`、`puppy_market_product_feature_cards`、`puppy_market_product_emoji`)和它们对应的死 CSS,把"商城页面链接""品牌分类法判断"这两处在 10 个地方各写一份的重复逻辑收进了 `puppy_market_catalog_url()`/`puppy_market_brand_taxonomy()` 两个共用函数里。

---

## 一、页面缺少内容:根因是数据/内容缺失,不是模板缺陷

逐页检查后,以下页面「看起来没内容」都能在代码里找到明确原因——模板本身都写好了空状态兜底,问题是后台数据和文案还没填。

| 页面 | 代码位置 | 根因 | 需要做的事 |
|---|---|---|---|
| 关于我们 / 联系我们 / 运费 / 退换货 / 隐私政策 | `functions.php` → `puppy_market_virtual_pages()` | 这 5 个页面**不是真实 WP 页面**,是拦截 404 请求后临时拼出来的,每个页面只有一句占位文案(例如运费页写着"Add your shipping zones, rates..."——明确提示这是待填内容) | 要么在后台建真实页面并写完整文案,要么保留虚拟页机制但把 `puppy_market_virtual_pages()` 里的占位字符串换成正式内容 |
| 商城首页 / 各分类页 | `functions.php` → `puppy_market_ensure_product_categories()` + `woocommerce_no_products_found` | 26 个分类**只创建了分类法词条**,README 里也写明"商品仍需在后台分配到分类"。新装的店多半所有分类下都是 0 商品,页面直接落到 `puppy_market_no_products_message()` 空状态 | 去后台把已有商品挂到对应分类;如果商品总数本来就少,先合并成少数几个大分类,避免大量"暂无商品"的分类页 |
| 首页畅销榜(Best sellers) | `front-page.php` 第 8 行,`orderby => popularity` | 排序依赖真实销量(`total_sales` meta),全新店铺没有订单历史,要么随机排序、要么因为没有已发布商品而落到"Customer favorites are coming soon"兜底 | 上线前至少手动跑几笔测试订单,或改成 `orderby => date` 过渡,等有真实销量再切回 popularity |
| 首页促销横滑(Sale carousel) | `front-page.php` 第 18 行,`on_sale => true` | 没有商品设置划线促销价时,直接落到"No sale items yet"兜底 | 给部分商品设置 Sale price |
| 商品详情页评论区 | `functions.php` → `puppy_market_custom_product_sections()` | 没有评论数据时输出"No reviews yet",评分分布条也是写死的示例百分比(`75/0/13/0/13`),不是真实计算 | 需要真实评论后展示才有意义;评分分布目前是假数据,后面要接真实统计或先隐藏这块 |
| 博客/Journal(`archive.php`/`single.php`/`search.php`) | 模板都实现了"Journal"文章列表,但站点定位是纯电商 | 大概率从没建过文章,`archive.php` 会一直显示"No stories yet" | 明确要不要做内容营销博客:不做就可以把导航/footer 里潜在的博客入口去掉,减少一个长期空状态入口;要做就得有内容排期 |
| 结算页(Checkout) | `style.css` 里只有 2 行 `.woocommerce-checkout` 相关规则 | 相比购物车页被改了 10 多轮,结算页基本是 WooCommerce 默认样式,视觉上会和其他页面脱节,"看起来像没做完" | 需要单独给 checkout 补一版和 cart/product 一致密度的样式(见下节技术债问题,建议先处理完 CSS 债务再动笔) |
| 我的账户订单历史 | WooCommerce 默认模板 | 没有真实订单时是标准空表格,没有 iPet 风格空状态文案 | 可选:加一个自定义空状态提示("Start shopping" 引导),优先级不高 |

**结论**:这一批问题本质是"新店铺没数据 + 5 个法务/说明页面从没写过正式文案",不是代码 bug。建议按上表优先级,先补分类挂载和 5 个说明页文案,这两项成本最低、对"看起来像没做完"的观感改善最大。

---

## 二、购物车页面:样式与 Chewy 不一致的根因

### 根因:`style.css` 里购物车相关规则被反复"追加式重写"了至少 7 轮,层层用 `!important` 互相覆盖

用注释标题搜了一遍,购物车(`body.woocommerce-cart`)相关的独立改版区块有:

```
line  926  /* Cart blocks */                                              — 基础版
line 1170  /* Cart page: compact two-column checkout layout... */         — 改版1
line 1264  /* Final cart pass: use the same full-width canvas... */       — 改版2(标题就叫 Final)
line 1308  /* Final cart density: shorter matching cards... */            — 改版3(又一个 Final)
line 1506  /* Cart sidebar: self-contained cards... */                    — 改版4
line 1588  /* Cart theme alignment: keep the storefront header/footer...*/ — 改版5(蓝色调、圆角卡片、阴影)
line 1776  /* Final cart spacing pass... */                               — 改版6(第三个 Final)
line 1801  /* Cart geometry fix... */                                     — 改版7,大量 !important
line 1926  /* Cart image and control refinement... */                    — 改版8,继续大量 !important
```

八轮修改全部作用在同一批选择器(`.wc-block-cart__main`、`.wc-block-cart-items__row`、`.wc-block-cart-item__image` 等)上,同一个属性(比如容器宽度 `width`)先后被设置了 5 次以上,最后一次带 `!important` 的生效。这种写法的直接后果:

1. **没人能一眼看出购物车"现在到底长什么样"**——要靠逐行读 CSS 才能拼出最终生效值,非常容易在下次改动时又叠加一层而不是修正已有规则。
2. **视觉上呈现"卡片化"而不是 Chewy 的"扁平化"**:改版5(line 1588 起)给购物车主容器加了 `border:1px solid #d9ebf2; border-radius:12px; background:#f5fbfd(浅蓝); box-shadow:0 2px 8px...`,侧边栏总价卡片、优惠卡片、推荐商品卡片全部各自带边框+圆角+阴影。Chewy 真实购物车是**白底、无阴影、行与行之间用一条 1px 浅灰分隔线**,没有这种"每个模块都是一张带阴影圆角卡片"的堆叠感。
3. **数量选择器/删除按钮反复被重新定位**:改版6-8 里,数量选择器的宽度先后被设成 172px→142px→110px,删除链接的位置和尺寸也被反复调整(28px 宽的图标按钮 vs 完整宽度按钮),说明这块布局一直没有定案。

### 具体样式差异(不含颜色)

| 维度 | 当前购物车(生效值) | Chewy 实际风格 |
|---|---|---|
| 商品行外框 | 每行商品图+信息整体包在一张 `border-radius:12px` + `box-shadow` 的卡片里(改版5) | 商品行之间只有一条 1px 实线分隔,整个列表**没有外框和阴影**,更像一张扁平表格 |
| 图片区域 | 图片区背景色不断变化(浅蓝底 → 透明底,改版8 才去掉"colored letterbox"),图片高度从固定 315px/300px 改到 `auto` | 图片本身不加背景色块,尺寸克制,不占据整行一半高度 |
| 圆角 | 主容器 12px,数量选择器 10px,汇总卡片 10-12px | 整体圆角很小甚至没有,分隔线为主而不是"卡片盒子"为主 |
| 侧边栏总价区 | 独立白卡片 + box-shadow + 圆角,和优惠券卡片、推荐商品卡片三个模块彼此分离、各自有边框 | 侧边栏通常是一整块无阴影区域,内部用细线分隔小节,不是三张独立卡片堆叠 |
| 数量/删除控件 | 反复调整宽度和位置(见上),目前是"数量选择器+28px 方形删除按钮"并排 | 通常是"数量下拉/步进器 + 文字链接『Remove』",不是纯图标按钮 |

---

## 三、建议的修改顺序

1. **先做 CSS 减法,再做加法**:把上面列出的 8 轮购物车改版**合并成一份**,只保留最终想要的规则,删掉被覆盖失效的历史层(这一步本身不改变任何视觉效果,只是把「一堆互相打架的规则」收敛成「一份可读的规则」,为后续真正的样式调整打地基)。checkout 页面同理,现在几乎是空的,合并购物车规则之后可以直接复用同一套 token,不用再单独摸索一遍。
2. **购物车视觉改版**(不含颜色):去掉商品行的卡片外框和阴影,改成扁平分隔线;图片区去掉背景色块,尺寸随内容自适应;侧边栏总价/优惠/推荐三个模块合并成一个整体区域内部用细线分节;数量控件统一成一种形态(建议"步进器 + 文字 Remove 链接"这种更贴近 Chewy 的组合)。
3. **补内容,而不是继续改样式**:第一节列的"分类挂商品""5 个说明页写正式文案""上线前跑测试订单"这几项,是让首页/商城/商品页看起来"完成度更高"的最快方式,优先级应该高于继续抠购物车的像素细节。
4. **决定博客/Journal 功能去留**:如果不打算做内容运营,建议从模板和导航里去掉这个入口,减少一个长期挂着"No stories yet"的页面。

---

## 四、新一轮排查(2026-08-08):更严格标准下,现在各页面还差什么

标准:除了配色方案、以及少量需要替代的内容(比如医药类目,可以用其他内容替代,不能直接照搬)之外,布局、密度、组件、交互都要和 Chewy 实际站点一致。阶段 0-7、A-F 已经解决了 header 结构、商品卡组件化、购物车/结算页扁平化、5 个说明页文案、评分假数据这几块。这一轮是在那之上重新过了一遍代码,找剩下的结构性差距——不是又一次颜色/文案层面的核对。

### Header / 导航

| 现状 | Chewy 实际结构 | 是否结构性缺口 |
|---|---|---|
| 账号入口是一个纯链接,点了直接跳转登录页/账户页 | 悬停或点击账号图标会弹出一个下拉面板(登录入口+订单快捷入口) | 是——`header.php` 里 `.header-menu-trigger` 现在没有任何下拉,这是组件缺失 |
| 购物车入口同样是纯链接,直接跳到 `/cart/` | 点击购物车图标弹出 mini-cart 预览抽屉(已加购商品缩略图+小计+去结算按钮),不用离开当前页 | 是——完全没有这个组件,`footer.php`/`header.php` 里都没有相关 DOM 或脚本 |
| 搜索框是纯 `<input type="search">`+提交按钮,没有联想 | 输入时弹出商品/关键词建议下拉 | 是,但优先级较低(需要一个轻量搜索建议接口,属于功能性组件而不是纯样式) |
| Mega menu 里没有"Pharmacy"类目 | Chewy 顶导航里有 Pharmacy | 不是缺口——这是你明确说可以用其他内容替代的部分,目前站内还没有放对应的替代类目(比如 Grooming/Services),可以在做 Header 相关阶段时顺带补一个 |

### 首页

- 商品卡(`template-parts/product-card.php`)现在只支持单个 `badge` 字段(渲染出一个徽章,例如"Sale"或"Best seller")。Chewy 的商品卡常见**多枚徽章叠加**(例如同时出现"Autoship & Save"+限时优惠),这是结构差异,不只是文案差异——需要 `product-card.php` 支持多徽章数组。
- 完全没有 Autoship / Subscribe & Save 相关的价格结构(一次性价 vs 订阅价对比、订阅折扣百分比)。这是 Chewy 商品卡和商品详情页共用的核心购买模式,目前全站搜索(`Autoship`/`subscribe`)零匹配,是结构性缺口而不是内容缺口。
- 没有"Shop by brand"品牌墙横条(Chewy 首页有一排常见品牌 logo 入口)。优先级较低。
- 已经做得比较到位、本轮没发现新问题的:三层 topbar+搜索、会员福利条(`value-strip`)、"Who are you shopping for"chip 导航、热门搜索 chip 行(`quick-links`)、畅销榜/促销横滑。

### 商城 / 分类页(`woocommerce.php`)

- 面包屑、宠物类型/品牌/价格/是否促销筛选已经在阶段 5 做了,结构上贴近 Chewy 的侧栏筛选。
- 侧栏没有"评分筛选"(Chewy 有"4 Stars & Up"这类 facet),这是结构缺口,建议补一个基于 `_wc_average_rating` meta 的星级筛选。
- 排序下拉(`woocommerce_catalog_ordering`)和结果计数(`woocommerce_result_count`)是 WooCommerce 默认输出,代码里没有移除,理论上还在——但阶段 5 只做了侧栏和面包屑,没有专门给这两个默认组件做过密度/样式适配,视觉上可能和新版卡片网格不搭调,需要找机会实机核对一下。

### 商品详情页

已经有的:图库缩略图切换(阶段 6)、尺码选择、手风琴式 About/Instructions/Size、评论区真实评分分布(阶段 E)。

本轮新发现的结构性缺口(不是可替代内容,是 Chewy 购买流程的核心组件,理应补齐):

1. **没有面包屑。** 商品详情页没有走 `woocommerce.php`(那是归档页模板),而是完全走 WooCommerce 插件自带的 `single-product/product.php`,主题里也没有挂 `woocommerce_before_main_content` 钩子加面包屑,所以商品页顶部现在是空的。Chewy 商品页顶部有完整的类目路径面包屑。
2. **没有 Autoship/Subscribe & Save 购买方式切换。** 这是 Chewy 加购区最核心的结构元素之一(一次性购买 vs 订阅省钱,通常是购买框里的一组单选)。目前 `functions.php` 里的 `puppy_market_product_size_picker()`、`puppy_market_product_promos()` 等钩子函数完全没有对应实现。
3. **没有"Frequently bought together"组合购买模块。**
4. **没有"Compare similar items"横向对比卡片。**
5. **没有预计到货/库存文案**(比如"Get it by Wed"或"In stock, ships in 1-2 days"),现在加购按钮旁边只有 `ipet-product-perks` 三条通用运费/退货/客服文案,没有这条商品级别的物流信息。

### 购物车 / 结算页

阶段 A-C 已经做完扁平化改版,本轮没有发现新的结构性问题。唯一遗留的是阶段 C 结算页选择器仍然标注"没有实机验证过",建议找机会核对一次是不是真的命中了 WooCommerce Blocks 结算页的类名。

### 我的账户(My Account)—— 本轮新发现,之前阶段 0-7/A-F 都没覆盖到

这一块之前完全没排查过,这次检查 `style.css` 发现两个问题:

1. **视觉语言还停留在改版之前的"胶囊圆角"风格**:导航链接 `border-radius: 999px`,内容面板 `border-radius: 20px`,和已经扁平化的购物车/结算页/说明页完全不是一套语言,是全站里最后一块没跟上的区域。
2. **CSS 本身有和当年购物车一样的重复堆叠问题**:`.woocommerce-MyAccount-navigation`/`.woocommerce-MyAccount-content` 相关选择器在 `style.css` 里出现了两组几乎同名的规则(约 1145-1158 行一组,1354-1435 行又一组),同一个属性被写了两遍,后一组靠源码顺序覆盖前一组生效——这正是购物车阶段 A 之前的那种技术债模式,只是规模小很多。

### Footer

5 列结构已经不错,和 Chewy footer 大方向一致。Chewy footer 还有支付方式图标行、App Store/Google Play 下载徽章、社交媒体图标行,这些是次要补充元素,优先级较低。

### 建议的阶段划分(等你确认后再开始)

- **阶段 H(商品详情页购买结构)**:补面包屑;加 Autoship/Subscribe & Save 购买方式切换(一次性价 vs 订阅价);加到货时间/库存文案。这是本轮发现里优先级最高的一块,因为购买框结构是 Chewy 最核心的辨识度来源。
- **阶段 I(商品卡多徽章 + Autoship 定价结构)**:`product-card.php` 支持多徽章数组;商品卡和详情页共用一套 Autoship 价格展示组件。
- **阶段 J(Header 交互组件)**:购物车预览抽屉(mini-cart);账号入口下拉面板;搜索联想放到低优先级单独处理,因为需要一个建议接口。同时把 Mega menu 里"用其他内容替代医药类目"这件事落地(比如加一个 Grooming/Services 入口)。
- **阶段 K(我的账户扁平化)**:先合并 `style.css` 里两组重复的 MyAccount 规则(减法,不改视觉),再把圆角/配色语言换成和购物车/结算页一致的扁平风格。
- **阶段 L(次要补充,优先级低)**:商城页评分筛选、排序下拉的实机样式核对、Footer 支付方式/App 下载徽章行、首页品牌墙。

---

## 附:购物车 CSS 技术债具体行号(方便后续合并时对照)

- 基础版:`style.css:926-950`
- 改版1(两栏结算布局):`style.css:1170-1262`
- 改版2(全宽画布):`style.css:1264-1306`
- 改版3(卡片密度):`style.css:1308-1376`
- 改版4(侧边栏自封装卡片):`style.css:1506-1587`(建议合并时一并核对)
- 改版5(蓝色调整体主题):`style.css:1588-1667`
- 改版6(间距):`style.css:1776-1799`
- 改版7(几何结构强制覆盖,大量 `!important`):`style.css:1801-1924`
- 改版8(图片/控件精修):`style.css:1926-1995`
