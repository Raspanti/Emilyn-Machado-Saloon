# Guia rápido: Git e GitHub para a equipe

Este guia é para quem está começando. Não precisa saber comandos avançados para colaborar.

## Antes da primeira tarefa

1. Tenha uma conta no GitHub e peça acesso ao repositório.
2. Configure seu nome e e-mail para os commits (uma única vez no seu computador):

   ```bash
   git config --global user.name "Seu Nome"
   git config --global user.email "seu-email-do-github"
   ```

3. Clone o projeto (uma única vez):

   ```bash
   git clone https://github.com/Raspanti/Emilyn-Machado-Saloon.git
   cd Emilyn-Machado-Saloon
   ```

Se preferir, use o Git integrado ao VS Code/VSCodium ou GitHub Desktop. O mesmo fluxo vale para todos.

## Fluxo de cada tarefa

### 1. Combine o que será feito

Crie ou escolha uma **Issue** no GitHub e avise a equipe para evitar duas pessoas fazendo a mesma tarefa.

### 2. Atualize sua cópia local e crie uma branch

```bash
git switch main
git pull --ff-only origin main
git switch -c feat/nome-da-tarefa
```

Troque `feat` por `fix` (correção) ou `docs` (documentação) quando fizer sentido.

**Nunca trabalhe diretamente na `main`.** Se a branch já existir, use `git switch nome-da-branch` em vez de criá-la novamente.

### 3. Trabalhe e revise o que mudou

```bash
git status
git diff
```

Faça commits pequenos, explicando a alteração:

```bash
git add caminho/do/arquivo
git commit -m "feat: criar página de serviços"
```

Prefira `git add caminho/do/arquivo` a adicionar tudo sem revisar. Não comite senhas, dados pessoais ou arquivos `.env`.

### 4. Envie a branch e abra um Pull Request

```bash
git push -u origin feat/nome-da-tarefa
```

Na página do GitHub, clique em **Compare & pull request**, selecione **base: main** e **compare: sua branch**, descreva o que fez e vincule a Issue (por exemplo, `Closes #12`).

Se ainda estiver trabalhando, marque o PR como **Draft**.

### 5. Peça revisão antes de integrar

Outra pessoa deve conferir o PR. Se pedir ajustes, faça novos commits na mesma branch e envie novamente com `git push`. Depois da aprovação, o PR pode ser integrado à `main` pelo responsável.

## Combinados da equipe

- **Uma tarefa = uma Issue = uma branch = um PR**, quando possível.
- Não usar `git push --force` na branch compartilhada, nem `git reset --hard` para “resolver” problemas sem entender o efeito.
- Se duas pessoas precisam alterar o mesmo arquivo, conversem antes.
- Evite commits com muitas mudanças sem relação.
- Registre no PR o que você testou e o que ainda não funciona.
- Não use dados reais de clientes no ambiente de desenvolvimento.
- Em caso de conflito, peça ajuda antes de sobrescrever arquivos ou descartar alterações.

## Glossário

| Termo | Significado |
| --- | --- |
| **Repositório** | Pasta do projeto com histórico de alterações |
| **Commit** | Registro de uma alteração |
| **Branch** | Linha de trabalho separada da principal |
| **Push** | Envia seus commits para o GitHub |
| **Pull** | Atualiza o repositório local com alterações remotas |
| **Issue** | Tarefa, problema ou sugestão organizada no GitHub |
| **Pull Request (PR)** | Pedido para revisar e integrar uma branch |
| **Merge** | Integra alterações de uma branch em outra |

## Comandos úteis quando algo parece errado

```bash
git status           # mostra branch e arquivos alterados
git branch --show-current
git diff             # mostra alterações ainda não commitadas
git log --oneline -5 # últimos commits
```

Esses comandos são seguros para consulta.

**Dúvida?** Abra uma Issue descrevendo o problema ou pergunte ao grupo antes de tentar comandos destrutivos.
