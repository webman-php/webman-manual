# Desempenho do webman

### Processo de tratamento de solicitações do framework tradicional

1. O nginx/apache recebe a solicitação.
2. O nginx/apache repassa a solicitação para o php-fpm.
3. O php-fpm inicializa o ambiente, como a criação de uma lista de variáveis.
4. O php-fpm chama o RINIT das várias extensões/módulos.
5. O php-fpm lê o arquivo PHP do disco (pode ser evitado usando o opcache).
6. O php-fpm faz análise léxica, análise sintática e compila o código em opcode (pode ser evitado usando o opcache).
7. O php-fpm executa o opcode, incluindo 8, 9, 10, 11.
8. O framework é inicializado, como a instância de várias classes, incluindo contêiner, controlador, roteador, middleware, etc.
9. O framework se conecta ao banco de dados e verifica permissões, conecta-se ao Redis.
10. O framework executa a lógica de negócios.
11. O framework encerra a conexão com o banco de dados e o Redis.
12. O php-fpm libera recursos, destrói todas as definições de classe e instâncias, destrói a tabela de símbolos, etc.
13. O php-fpm chama sequencialmente o método RSHUTDOWN das várias extensões/módulos.
14. O php-fpm encaminha o resultado para o nginx/apache.
15. O nginx/apache retorna o resultado para o cliente.

### Processo de tratamento de solicitações do webman

1. O framework recebe a solicitação.
2. O framework executa a lógica de negócios.
3. O framework retorna o resultado para o cliente.

De fato, na ausência de reversão do nginx, o framework tem apenas essas três etapas. Pode-se dizer que este é o ápice do framework PHP, tornando o desempenho do webman várias vezes ou até dezenas de vezes melhor do que o framework tradicional.

Para mais informações, consulte [Teste de estresse](benchmarks.md).
