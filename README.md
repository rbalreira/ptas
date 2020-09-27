# Projeto Temático em Aplicações SIG
Projeto no âmbito do [Módulo Temático em Aplicações SIG](https://www.ua.pt/pt/uc/13449) da licenciatura em [Tecnologias da Informação](https://www.ua.pt/pt/curso/63) da [Universidade de Aveiro - Escola Superior de Tecnologia e Gestão de Águeda](https://www.ua.pt/pt/estga).

# Objetivo
Implementação de uma aplicação WebSIG que representa geograficamente a componente desportiva da região de Aveiro e da Universidade de Aveiro e seus politécnicos. Possibilita, assim, que o utilizador encontre toda a informação pretendida de cada clube, equipa e das infraestruturas desportivas que existem na região de Aveiro. Restringindo a informação que é vista no mapa, o utilizador pode filtrar por várias condições: município (Ílhavo, Ovar, Estarreja…), freguesia (Borralha, Angeja, Recardães…), modalidade (futebol, basquetebol, andebol…), escalão (juniores, seniores), género (masculino e/ou feminino), se o campo é ou não coberto, se se pratica ou não desporto adaptado e preço.

# Tecnologias
- [Microsoft Project](https://www.microsoft.com/pt-pt/microsoft-365/project/project-management-software)
- UML
- [Git](https://git-scm.com/)
- HTML
- CSS
- Javascript
- [jQuery](https://jquery.com/)
- PHP
- [OpenLayers](https://openlayers.org/)
- [ol-ext](https://viglino.github.io/ol-ext/)
- [Bootstrap](https://getbootstrap.com/)
- [FontAwesome](https://fontawesome.com/)
- [SweetAlert2](https://sweetalert2.github.io/)
- [PostgreSQL](https://www.postgresql.org/)
- [PostGIS](https://postgis.net/)
- [pgRouting](https://pgrouting.org/)

# Instalação
1. Criar uma base de dados no PostgreSQL, usando a linha de comandos ou um GUI ([pgAdmin](https://www.pgadmin.org/), [DBeaver](https://dbeaver.io/)...).
2. Alterar o role "[postgres]" do ficheiro de backup [database.sql](database.sql) para o role usado na base de dados criada no ponto anterior.
3. Importar o ficheiro de backup da base de dados [database.sql](database.sql) para a base de dados criada no ponto 1.
4. Abrir o programa em java num IDE ([Netbeans](https://netbeans.org/) ou [Eclipse](https://www.eclipse.org/)).
5. Alterar as credenciais de acesso do ficheiro [Conexao.java](app/src/connection/Conexao.java) que faz conexão à base de dados.
6. Executar o programa.

# Documentação
- [Relatório](report.pdf)

# Autores
- Daniel Martins
- Jorge Anjos
- Pedro Ferreira
- Ricardo Balreira
- Rui Duarte
