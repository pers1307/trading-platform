# Общие директивы

1. Используй русский язык
2. Отвечай профессионально, как высококлассный опытный специалист
3. Если нужно что-то уточнить для увеличения качества ответа на вопрос, задавай мне вопросы до формирования ответа
4. Всегда старайся перепроверять выполненную работу или твой ответ (например, найти подтверждение в первоисточниках или перепроверить расчет другим способом)
5. Прямолинейность и точность важнее согласия со мной. Придерживайся скептической, основанной на доказательствах позиции: оспаривай мои предположения и предоставляй лучшие контраргументы желательно с указанием источников. Разделяй факты/выводы/мнения, указывай уверенность при сомнениях в точности информации. Начинай с краткого резюме, затем приводи детали

# Когда получаешь инструкции для выполнения нового кода, необходимо придерживаться следующего флоу
1. Всегда составляй пошаговый и подробный план реализации команды
2. Анализируй проект
3. Задай уточняющие вопросы
4. Реализуй решение
5. Покритикуй выполненное решение. Предложи как его можно улучшить
6. После реализации решения выполняй полный прогон тестов и анализаторов

## Project Structure & Module Organization
- Application code lives in `src/` (controllers, services, entities, Twig helpers); view templates in `templates/`; public assets served from `public/`.
- Domain tests sit in `tests/Unit` and `tests/Integration`; HTTP/UI checks in `tests/Functional` and `tests/Behavioral`; shared helpers/scripts in `tests/Script`.
- Migrations are under `migrations/`; framework/config defaults in `config/`; Docker tooling in `docker/` with compose file `docker-compose.yaml` and helper targets in `Makefile`.

## Build, Test, and Development Commands
- `make build` / `make build-no-cache`: build Docker images.
- `make up` / `make down` / `make stop`: start/tear down the stack; `make in` opens a shell in the PHP container.
- `./test.sh`: reloads the test DB via `tests/Script/Helpers` and runs the full PHPUnit suite.
- `./unit.sh` or `php bin/phpunit --testsuite Unit`: fast unit pass; add `--filter <Name>` for focused runs.
- `make migrations-diff` / `make migrations-up`: generate and apply Doctrine migrations (Xdebug disabled for speed).

## Coding Style & Naming Conventions
- Follow PSR-12: 4-space indent, braces on new lines, typed properties/params, and strict namespaces under `App\`.
- Classes/DTOs/Services use `PascalCase`; methods `camelCase`; Twig templates and routes prefer lowercase with hyphens/underscores for readability.
- Keep controllers thin and push domain logic into services; reuse DTOs and repositories under `src/`.
- Avoid committing secrets; `.env.local` overrides `.env` and stays untracked.

## Testing Guidelines
- Primary framework: PHPUnit (see `phpunit.xml`). Use data providers for matrixed cases.
- Place new tests alongside code: `tests/Unit` for pure domain logic, `Integration` for persistence, `Functional/Behavioral` for HTTP flows.
- Name tests descriptively: `methodName_should_doExpectedThing`.
- Maintain TDD habit from this codebase history; update fixtures or factories in `tests/Script` when schema changes.

## Commit & Pull Request Guidelines
- Commits are short and descriptive (often in Russian); keep them scoped to a single concern, e.g., “Обновил обработку рисков”.
- Ensure tests pass locally (`./test.sh`) before pushing; include migration files when changing entities.
- Pull requests should describe intent, list key changes, reference related issues/tasks, and attach screenshots for UI-affecting work (Dashboards, Widgets, AdminLTE pages).
- Document new console commands/cron expectations in `README.md` if applicable and note any env var additions.

## Security & Configuration Tips
- Keep database credentials, tokens (Telegram/Finam), and API keys in env vars; never commit them.
- When adding cron-like logic, prefer Symfony commands under `bin/console` and document schedules to keep ops in sync.
