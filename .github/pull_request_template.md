## 变更说明 / Summary

请说明问题、方案、影响范围并关联 Issue。

Describe the problem, solution, affected scope, and related Issue.

## 变更类型 / Type

- [ ] 缺陷修复 / Bug fix
- [ ] 新增能力 / New capability
- [ ] 重构或性能改进 / Refactoring or performance
- [ ] 文档或维护 / Documentation or maintenance
- [ ] 破坏性变更 / Breaking change

## 兼容与安全 / Compatibility and Security

- [ ] 保持 PHP `>=5.6`，未引入高版本专属语法 / Preserves PHP `>=5.6` without newer-only syntax
- [ ] 公共 API 兼容，或已说明迁移方式 / Preserves public API compatibility or documents migration
- [ ] 新依赖已评估版本、维护与许可证 / New dependencies were reviewed for versions, maintenance, and licensing
- [ ] 已评估安全、敏感数据和并发影响 / Security, sensitive-data, and concurrency impact was reviewed

## 验证 / Verification

- [ ] `composer validate --strict`
- [ ] `composer lint`
- [ ] `composer test`
- [ ] 已补充相关测试，或说明无需补充 / Relevant tests were added, or an explanation was provided
- [ ] 已说明未执行的验证及原因 / Skipped checks and reasons are documented

请提供必要的验证摘要，不要粘贴包含凭据或业务数据的完整日志。

Provide a concise verification summary without credentials or sensitive business data.

## 文档与发布 / Documentation and Release

- [ ] 已同步相关中英文公开入口，或说明无需更新 / Relevant Chinese and English public entries are synchronized or marked not applicable
- [ ] 已更新 `CHANGELOG.md`“未发布”章节，或说明无需更新 / The `Unreleased` changelog is updated or marked not applicable
- [ ] 已评估 SemVer 与发布影响 / SemVer and release impact were reviewed

## 提交者确认 / Contributor Confirmation

- [ ] 变更范围单一且已自审 diff / The change is focused and the diff was self-reviewed
- [ ] 未包含密钥、个人数据、日志、`vendor/` 或 IDE 文件 / No secrets, personal data, logs, `vendor/`, or IDE files are included
- [ ] 我有权提交并同意按 MIT License 发布 / I have the right to contribute this work under the MIT License

<!-- 安全敏感变更请按 SECURITY.md / SECURITY.en.md 私下协调，不要公开可利用细节。 -->
