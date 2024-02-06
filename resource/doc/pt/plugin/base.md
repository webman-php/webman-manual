# Plugins Básicos

Os plugins básicos geralmente são componentes genéricos que são comumente instalados usando o Composer e colocados no diretório "vendor". Durante a instalação, é possível copiar automaticamente algumas configurações personalizadas (middlewares, processos, rotas, etc.) para o diretório `{projeto principal}/config/plugin`, e o webman automaticamente reconhecerá essas configurações e as mesclará com a configuração principal, permitindo assim que os plugins intervenham em qualquer etapa do ciclo de vida do webman. 

Mais informações em [Criação de Plugins Básicos](create.md)
