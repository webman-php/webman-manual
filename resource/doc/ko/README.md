webman은 고성능의 HTTP 서비스 프레임워크로서 [workerman](https://www.workerman.net)를 기반으로 개발되었습니다. webman은 전통적인 php-fpm 아키텍처를 대체하여 매우 높은 성능과 확장 가능한 HTTP 서비스를 제공합니다. webman을 사용하여 웹사이트를 개발하거나 HTTP API 또는 마이크로서비스를 개발할 수 있습니다.

또한 webman은 사용자 정의 프로세스를 지원하여 workerman이 가능한 모든 작업을 수행할 수 있습니다. 예를 들어 웹소켓 서비스, 사물인터넷, 게임, TCP 서비스, UDP 서비스, Unix 소켓 서비스 등을 구현할 수 있습니다.

webman의 철학은 **가장 작은 내부 커널로 최대의 확장성과 최고의 성능을 제공하는 것**입니다.

webman은 핵심 기능(라우팅, 미들웨어, 세션, 사용자 정의 프로세스 인터페이스)만 제공하며, 나머지 기능은 모두 컴포저 에코시스템을 재사용합니다. 즉, webman에서는 가장 익숙한 기능 구성 요소를 사용할 수 있으며, 예를 들어 데이터베이스 측면에서는 Laravel의 `illuminate/database`, ThinkPHP의 `ThinkORM` 등을 선택할 수 있습니다. 그리고 이러한 구성 요소를 webman에 통합하는 것은 매우 쉽습니다.

webman의 특징은 다음과 같습니다.

1. 고 안정성: webman은 workerman을 기반으로 하며, workerman은 업계에서 버그가 거의 없는 고 안정성 소켓 프레임워크입니다.
2. 초고성능: webman은 전통적인 php-fpm 프레임워크보다 10-100배 더 높은 성능을 제공하며, go의 gin, echo 등과 같은 프레임워크보다도 약간 더 높은 성능을 보입니다.
3. 고 재사용성: 거의 모든 컴포저 구성 요소 및 클래스 라이브러리를 수정하지 않고 재사용할 수 있습니다.
4. 고 확장성: 사용자 정의 프로세스를 지원하여 workerman이 가능한 모든 작업을 수행할 수 있습니다.
5. 매우 간단하고 쉬운 사용법으로 학습 비용이 매우 낮으며, 코드 작성은 전통적인 프레임워크와 유사합니다.
6. 가장 널널한 MIT 오픈 소스 라이선스를 사용합니다.

프로젝트 주소는 다음과 같습니다.

GitHub: https://github.com/walkor/webman **별을 아끼지 말아주세요**

Gitee: https://gitee.com/walkor/webman **별을 아끼지 말아주세요**

외부의 권위있는 벤치마크 데이터는 다음과 같습니다.

![](../assets/img/benchmark1.png)

데이터베이스 쿼리 비즈니스로 인한 webman의 단일 서버 처리량은 39만 QPS에 이르며 전통적인 php-fpm 아키텍처의 라라벨 프레임워크보다 약 80배 높습니다.

![](../assets/img/benchmarks-go.png)

데이터베이스 쿼리 비즈니스로 인한 webman은 동일 유형의 go 언어 웹 프레임워크의 성능을 약 두 배 높입니다.

위 데이터는 [techempower.com](https://www.techempower.com/benchmarks/#section=data-r20&hw=ph&test=db&l=zik073-sf)에서 가져온 것입니다.
