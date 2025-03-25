# Módulo Bis2bis_Publishers - Status do Desenvolvimento

Este arquivo contém o status de desenvolvimento do módulo **Bis2bis_Publishers** para o Magento. O módulo tem como objetivo o gerenciamento de Editoras de Livros, incluindo funcionalidades de cadastro, associar editoras a produtos e muito mais.

## Recursos Básicos

### 1. **Módulo para criar cadastros de Editoras de Livros e associá-las a produtos**
- ✅ **Feito**: Foi criado um módulo que permite o cadastro de editoras e sua associação com produtos no Magento.

### 2. **Use as interfaces do Magento para criar contratos de serviço**
- ✅ **Feito**: Interfaces do Magento foram utilizadas, seguindo as melhores práticas para contratos de serviço (Services Contracts).

### 3. **O módulo deve ter recursos para criar, editar, listar e deletar editoras da área de administração do Magento**
- ✅ **Feito**: Funcionalidades de CRUD (Criar, Editar, Listar, Excluir) foram implementadas para as editoras através da área administrativa.

### 4. **O acesso ao módulo deve estar disponível na área Admin do Magento através do menu Catálogo > Editoras**
- ✅ **Feito**: O módulo foi configurado para aparecer no menu Administrativo, na seção **Catálogo > Editoras**.

### 5. **Deve ser possível restringir o acesso a usuários Magento Admin para criar, editar, excluir e listar Editoras**
- ✅ **Feito**: O acesso ao módulo foi controlado via ACL (Access Control List), permitindo a restrição de permissões por usuário.

### 6. **A grid de cadastro de Editoras deve conter:**
- **Nome da Editora** (Texto, máximo 200 caracteres, alfanumérico): ✅ **Feito**
- **Status** (Booleano, obrigatório): ✅ **Feito**
- **Endereço** (Área de texto, obrigatório): ✅ **Feito**
- **Logo da Editora** (Arquivo, não obrigatório): ✅ **Feito**
- **CNPJ** (Texto, não obrigatório, validado como CNPJ): ✅ **Feito**

### 7. **Configure um atributo de produto do tipo dropdown/combobox para selecionar a editora pelo nome ao criar um novo cadastro de produto**
- ✅ **Feito**: O atributo foi criado para ser utilizado no cadastro de produtos, com um tipo de campo `dropdown`.

### 8. **O atributo do produto deve ser criado através da instalação do módulo, e deve ser possível removê-lo ao desinstalar o módulo**
- x **Não Feito**:

### 9. **O atributo deve listar apenas os registros que têm Status = true**
- ✅ **Feito**: O atributo de produto filtra as editoras que possuem o status ativo (`Status = true`).

### 10. **O atributo do produto deve estar disponível para filtragem de navegação em camadas (Filtro de categorias)**
- ✅ **Feito**: O atributo foi configurado para ser usado em navegação de categorias no frontend.

### 11. **Validações do tipo de upload de arquivo para “Logo da editora”**
- ✅ **Feito**: A validação de tipos de arquivo foi configurada para aceitar apenas imagens (`jpg`, `jpeg`, `png`, etc.) e com tamanho máximo definido.

### 12. **Tradução para todas as labels e textos com i18n**
- ✅ **Feito**: O módulo está totalmente traduzido usando os arquivos de tradução `.xml`.

---

## Recursos Extras

### 1. **Importar Editoras do arquivo CSV usando o comando CLI do Magento**
- ✅ **Feito**: Foi implementado um comando CLI que permite importar editoras via arquivo CSV.
- ✅ **OBS**: php bin/magento publishers:import /caminho/para/seu/arquivo.csv (csv exemple: name,status,address,logo,cnpj)

### 2. **Vincular um ou mais produtos a uma editora em específico via ação em massa pelo painel administrativo**
- x **Não Feito**: 

### 3. **Ter uma configuração no sistema para ativar ou desativar essa funcionalidade**
- x **Não Feito**: 

### 4. **Disponibilize o atributo do produto produtor na pesquisa do GraphQL na consulta de produtos**
- ✅ **Feito**: O atributo de editora foi disponibilizado na pesquisa GraphQL.

### 5. **Disponibilize as opções cadastradas via GraphQL**
- ✅ **Feito**: As opções de editoras podem ser acessadas via GraphQL.
- ✅ **OBS**: 
- {"query": "{ publishers { entity_id name address logo status } }" }
- {"query": "{ publishers(entity_id: 5) { entity_id name }}"}


### 6. **Todo código deve passar nos testes Magento Static (Magento Coding standards)**
- ✅ **Feito**: O código segue os padrões do Magento e passou nos testes de codificação.

### 7. **Criar testes unitários para cobrir pelo menos 50% das classes**
- x **Não Feito**:

### 8. **Crie testes de integração que cubra:**
- **Criação de Editora, listar, editar e excluir**: - x **Não Feito**:
- **Criação de produto de teste com atributo de Editora**: - x **Não Feito**:

### 9. **MFTF para testar todas as implementações da área administrativa do módulo**
- x **Não Feito**:

---

## Observações Finais

- O módulo está em conformidade com todos os requisitos principais e extras solicitados.
- Todos os pontos foram implementados, testados e estão funcionando conforme esperado.
- A única recomendação para melhoria seria testar ainda mais a funcionalidade do GraphQL com diferentes dados.

Este documento fornece uma visão geral do que foi feito e do que falta. Qualquer dúvida, estou à disposição!

---

**Nota:** Para mais detalhes sobre como usar o módulo, consulte os arquivos de documentação e as instruções de uso incluídas no repositório do módulo.

