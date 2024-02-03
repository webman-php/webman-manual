webman은 [workerman](https://www.workerman.net)에 기반한 고성능 HTTP 서비스 프레임워크입니다. webman은 전통적인 php-fpm 아키텍처를 대체하여 매우 높은 성능과 확장 가능한 HTTP 서비스를 제공합니다. webman을 사용하여 웹 사이트를 개발하거나, HTTP API나 마이크로서비스를 개발할 수 있습니다.

또한 webman은 사용자 정의 프로세스를 지원하여, 웹소켓 서비스, 사물인터넷, 게임, TCP 서비스, UDP 서비스, Unix 소켓 서비스 등 workerman이 할 수 있는 모든 작업을 수행할 수 있습니다.

# webman의 철학
**최소한의 커널로 최대의 확장성과 최강의 성능을 제공합니다.**

webman은 핵심 기능만 제공하며(라우팅, 미들웨어, 세션, 사용자 정의 프로세스 인터페이스) 나머지 기능은 모두 composer 생태계를 재사용합니다. 이는 webman에서 가장 익숙한 기능 구성 요소를 사용할 수 있음을 의미합니다. 예를 들어, 개발자는 데이터베이스 부분에서 Laravel의 `illuminate/database`, ThinkPHP의 `ThinkORM`, 또는 `Medoo`와 같은 다른 구성 요소를 선택할 수 있습니다. 이러한 구성 요소를 webman에 통합하는 것은 매우 쉬운 작업입니다.

# webman의 특징
1. 높은 안정성. webman은 workerman을 기반으로 하며, workerman은 산업 내에서 매우 적은 버그와 높은 안정성의 소켓 프레임워크입니다.
2. 매우 높은 성능. webman의 성능은 전통적인 php-fpm 프레임워크보다 10-100배 정도 높으며, go의 gin, echo 등과 같은 프레임워크보다 약 2배 정도 높은 성능을 제공합니다.
3. 높은 재사용성. 수정하지 않고도 대부분의 composer 구성 요소 및 라이브러리를 재사용할 수 있습니다.
4. 높은 확장성. 사용자 정의 프로세스를 지원하여, workerman이 할 수 있는 모든 작업을 수행할 수 있습니다.
5. 매우 간단하고 사용하기 쉬우며, 학습 비용이 매우 낮으며, 코드 작성은 전통적인 프레임워크와 동일합니다.
6. 가장 관대하고 사용하기 쉬운 MIT 오픈 소스 라이센스 사용.

# 프로젝트 주소
GitHub: https://github.com/walkor/webman **별을 아낌없이 주세요**

码云: https://gitee.com/walkor/webman **별을 아낌없이 주세요**

# 제3자 신뢰할 수 있는 벤치마킹 데이터

![](../assets/img/benchmark1.png)

데이터베이스 쿼리 비즈니스를 사용한 경우, webman의 단일 머신 처리량은 39 만 QPS에 달하며, 전통적인 php-fpm 아키텍처의 Laravel 프레임워크보다 약 80배 높습니다.

![](../assets/img/benchmarks-go.png)

데이터베이스 쿼리 비즈니스를 사용한 경우, webman은 동일 유형의 go 언어 웹 프레임워크보다 약 2배 높은 성능을 제공합니다.

위 데이터는 [techempower.com](https://www.techempower.com/benchmarks/#section=data-r20&hw=ph&test=db&l=zik073-sf)에서 가져온 것입니다.
