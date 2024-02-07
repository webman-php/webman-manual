# Desempenho do webman


### Fluxo de processamento de solicitação do framework tradicional

1. O nginx/apache recebe a solicitação.
2. O nginx/apache repassa a solicitação ao php-fpm.
3. O php-fpm inicializa o ambiente, como a criação de uma lista de variáveis.
4. O php-fpm chama o RINIT de várias extensões/módulos.
5. O php-fpm lê o arquivo php do disco (pode ser evitado usando o opcache).
6. O php-fpm realiza a análise léxica, análise sintática e compilação em opcode (pode ser evitado usando o opcache).
7. O php-fpm executa o opcode, incluindo 8, 9, 10, 11.
8. O framework é inicializado, como a instanciação de várias classes, incluindo contêiner, controladores, rotas, middlewares etc.
9. O framework se conecta ao banco de dados e verifica a permissão, conecta-se ao redis.
10. O framework executa a lógica de negócios.
11. O framework encerra a conexão com o banco de dados e o redis.
12. O php-fpm libera recursos, destrói todas as definições de classe, instâncias, destrói a tabela de símbolos etc.
13. O php-fpm chama sequencialmente o método RSHUTDOWN de várias extensões/módulos.
14. O php-fpm encaminha o resultado para o nginx/apache.
15. O nginx/apache retorna o resultado para o cliente.


### Fluxo de processamento de solicitação do webman

1. O framework recebe a solicitação.
2. O framework executa a lógica de negócios.
3. O framework retorna o resultado para o cliente.

Sim, sem a situação de proxy reverso do nginx, o framework possui apenas essas 3 etapas. Pode-se dizer que isso é o ápice de um framework PHP, o que torna o desempenho do webman várias vezes ou até dezenas de vezes melhor do que o framework tradicional.

Para mais informações, consulte [Teste de estresse](benchmarks.md)
