# Plugins Básicos

Os plugins básicos geralmente são componentes genéricos que são geralmente instalados usando o Composer e colocados na pasta `vendor`. Durante a instalação, é possível que alguns configurações personalizadas (como middlewares, processos, rotas, etc.) sejam automaticamente copiadas para o diretório `{diretório do projeto principal}/config/plugin`. O Webman reconhecerá automaticamente essas configurações no diretório e as mesclará com a configuração principal, permitindo que os plugins se envolvam em qualquer fase do ciclo de vida do Webman.

Para mais informações, consulte [Criação de Plugins Básicos](create.md).
