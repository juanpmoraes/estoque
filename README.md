# 📦 Sistema de Estoque com Visualização Animada

Projeto completo em PHP + MySQL para controle de estoque físico, incluindo interface moderna em HTML/CSS, animação visual de câmera via JS e busca dinâmica por posição. Ideal para centros logísticos, armazéns industriais e cenários onde é importante localizar caixas rapidamente em uma planta.

---

## 🚀 Funcionalidades

- Cadastro de caixas com código, descrição e quantidade.
- Controle estrutural do estoque: 14 ruas, 4 prateleiras por rua, cada com 18 colunas e até 3 caixas por coluna.
- Visualização em grid com CSS Grid, simulando a planta real do armazém.
- Animação de câmera: a interface simula uma “viagem” até a célula destino usando pan/zoom suave por JS e scrollIntoView.
- Busca por posição (rua, coluna, linha) ou por código do item.
- Alocação, movimento e remoção de caixas em slots físicos.
- Destaque visual do destino no padrão Rxx-yy-zz na interface.
- Validação para evitar excesso de caixas por coluna.
- Estrutura separada por pastas para segurança e manutenção.

---

## 🎯 Estrutura de Pastas

estoque/
├── public/
│ ├── index.php
│ ├── nova_caixa.php
│ ├── salvar_caixa.php
│ ├── alocar.php
│ ├── salvar_alocacao.php
│ ├── gerir_coluna.php
│ ├── remover_ocupacao.php
│ ├── mover_ocupacao.php
│ ├── buscar_posicao.php
│ └── assets/
│ └── css/
│ └── styles.css
├── src/
│ └── db.php
├── database/
│ └── schema.sql
├── README.md


---

## 🛠️ Tecnologias Utilizadas

- PHP 7+
- MySQL 5.7+ (ou MariaDB)
- HTML5, CSS3 (inclui CSS Grid e transições)
- JavaScript puro (sem dependências externas)
- PDO para conexão segura ao banco

---

## ⚡ Instalação e Execução

1. Clone o projeto:
    ```
    git clone https://github.com/juanpmoraes/estoque.git
    ```

2. Importe `database/schema.sql` no seu MySQL (pode usar phpMyAdmin, DBeaver, etc).

3. Atualize `src/db.php` com seu usuário/senha do banco.

4. Configure para acessar via navegador, apontando o DocumentRoot para a pasta `public/`.

5. Acesse `index.php` e comece a cadastrar e localizar itens pelo grid animado.

---

## 📸 Demonstração Visual

- Visualização do estoque em planta.
- Recorte animado de câmera até célula de destino (busca por código ou posição).
- Destaque da célula com etiqueta personalizada `Rxx-yy-zz`.

---

## 🌟 Contribuição

Pull requests são bem-vindos! Siga o fluxo de branches, nomeie de acordo (`feature/nome`, `fix/nome`), descreva mudanças e garanta que funcione localmente antes do PR.

---

## 📝 Licença

Este projeto é open-source sob licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

---

## 🙌 Referências e Inspirado em

- [Como escrever um README incrível](https://www.alura.com.br/artigos/como-escrever-um-readme-incrivel-no-github) [web:89]
- [Modelos para README](https://gist.github.com) [web:87]
- [Estrutura de README.md para projetos](https://dio.me) [web:90]

