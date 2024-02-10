# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased](https://github.com/figuren-theater/ft-maintenance/compare/1.3.0...HEAD)

## [1.3.0](https://github.com/figuren-theater/ft-maintenance/compare/1.2.2...1.3.0) - 2024-02-10

### 🚀 Added

- Use WordPress core functions from within the Dashboard-Widget to erase the content of log files ([#56](https://github.com/figuren-theater/ft-maintenance/pull/56))

### 🐛 Fixed

- Migrate 'Site Health Check' related code from 'deprecated_Figuren_Theater__v2' and 'ft-routes' ([#55](https://github.com/figuren-theater/ft-maintenance/pull/55))

### Dependency Updates & Maintenance

- Bump johnbillion/query-monitor from 3.13.1 to 3.15.0 ([#53](https://github.com/figuren-theater/ft-maintenance/pull/53))
- Bump johnbillion/wp-crontrol from 1.15.3 to 1.16.1 ([#54](https://github.com/figuren-theater/ft-maintenance/pull/54))

## [1.2.2](https://github.com/figuren-theater/ft-maintenance/compare/1.2.1...1.2.2) - 2023-10-14

### 🚀 Added

- Enable 'wp_crontrol' for all runs, not only when WP_DEBUG is enabled ([#49](https://github.com/figuren-theater/ft-maintenance/pull/49))

### Dependency Updates & Maintenance

- Set fixed versions for all 3rd-party-deps & UPDATE internal deps ([#50](https://github.com/figuren-theater/ft-maintenance/pull/50))

## [1.2.1](https://github.com/figuren-theater/ft-maintenance/compare/1.2.0...1.2.1) - 2023-09-13

- Deprecate FT_PLATTFORM_VERSION constant, use a fn instead ([#47](https://github.com/figuren-theater/ft-maintenance/pull/47))

## [1.2.0](https://github.com/figuren-theater/ft-maintenance/compare/1.1.0...1.2.0) - 2023-09-02

### Security

- Avoid hard-coded strings ([#43](https://github.com/figuren-theater/ft-maintenance/pull/43))
- Add NonceVerification for clearing the logfile like CS recommended ([#42](https://github.com/figuren-theater/ft-maintenance/pull/42))

### Dependency Updates & Maintenance

- Updated 9 deps ([#45](https://github.com/figuren-theater/ft-maintenance/pull/45))

## [1.1.0](https://github.com/figuren-theater/ft-maintenance/compare/1.0.15...1.1.0) - 2023-08-10

### 🚀 Added

- Establish quality standards ([#27](https://github.com/figuren-theater/ft-maintenance/pull/27))

### 🐛 Fixed

- Fix CS issues ([#30](https://github.com/figuren-theater/ft-maintenance/pull/30))
- Develop ([#24](https://github.com/figuren-theater/ft-maintenance/pull/24))
- Develop ([#22](https://github.com/figuren-theater/ft-maintenance/pull/22))
- Develop ([#20](https://github.com/figuren-theater/ft-maintenance/pull/20))
- Develop ([#16](https://github.com/figuren-theater/ft-maintenance/pull/16))
- Bump version ([#13](https://github.com/figuren-theater/ft-maintenance/pull/13))
- feat Develop ([#11](https://github.com/figuren-theater/ft-maintenance/pull/11))

### Dependency Updates & Maintenance

- Bump johnbillion/wp-crontrol from 1.15.2 to 1.15.3 ([#33](https://github.com/figuren-theater/ft-maintenance/pull/33))
- Bump johnbillion/wp-crontrol from 1.15.1 to 1.15.2 ([#17](https://github.com/figuren-theater/ft-maintenance/pull/17))
- Bump johnbillion/query-monitor from 3.12.3 to 3.13.1 ([#31](https://github.com/figuren-theater/ft-maintenance/pull/31))
- Bump johnbillion/query-monitor from 3.12.1 to 3.12.3 ([#23](https://github.com/figuren-theater/ft-maintenance/pull/23))
- Bump johnbillion/query-monitor from 3.11.2 to 3.12.1 ([#19](https://github.com/figuren-theater/ft-maintenance/pull/19))
- Bump figuren-theater/ft-options from 1.1.7 to 1.1.10 ([#12](https://github.com/figuren-theater/ft-maintenance/pull/12))
